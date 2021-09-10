<?php defined('_JEXEC') or die('Restricted access');

/**
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

jimport('joomla.utilities.date');

JHTML::_('behavior.modal', 'a.html5-modal');
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.loadCss');

$currentMenuItem = JFactory::getApplication()->getMenu()->getActive();
if (isset($currentMenuItem))
    $colNumb = $currentMenuItem->params->get('c_colnumb', 1);
else
    $colNumb = 1;

$uri = JUri::getInstance();
$jinput = JFactory::$application->input;

require_once (JPATH_SITE . '/components/com_html5flippingbook/libs/Mobile_Detect.php');
$detectMobile = new Mobile_Detect_HTML5FB();

$doc = JFactory::getDocument();

// Exclude tablets.
$isMobile = FALSE;
$isTablet = FALSE;
if ($detectMobile->isMobile() && !$detectMobile->isTablet())
{
	$isMobile = TRUE;
}
elseif ($detectMobile->isTablet())
{
	$isTablet = TRUE;
}

if ( @$this->item->opengraph_use )
{
	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/MethodsForXml.php');

	$pageLink = JUri::base().JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id, false, (int)$uri->isSSL());

	$opengraphTitle = ($this->item->opengraph_title != '' ? $this->item->opengraph_title : $this->item->c_category);
	$opengraphAuthor = ($this->item->opengraph_author != '' ? $this->item->opengraph_author : '');
	$imageFileName = ($this->item->opengraph_image != '' ? $this->item->opengraph_image : '');
	$imageFullFileName = COMPONENT_MEDIA_PATH.'/thumbs/'.$imageFileName;

	if ($imageFileName != '' && is_file($imageFullFileName)) $opengraphImage = COMPONENT_MEDIA_URL.'thumbs/'.$imageFileName;
	else $opengraphImage = '';

	if ($this->item->opengraph_description != '')
		$opengraphDesc = $this->item->opengraph_description;
	else
	{
		$noTagsDescr = preg_replace('/<[^>]*>/', '', $this->item->c_instruction);
		$opengraphDesc = (strlen($noTagsDescr) <= 100 ? $noTagsDescr : substr($noTagsDescr, 0, 100).'...');
	}

	if ( !empty($this->config->social_facebook_og_app_id) )
	{
		$doc->addCustomTag('<meta property="fb:admins" content="'.$this->config->social_facebook_og_admin_id.'" />');
		$doc->addCustomTag('<meta property="fb:app_id" content="'.$this->config->social_facebook_og_app_id.'" />');
	}

	$doc->addCustomTag('<meta property="og:url" content="'. $pageLink.'" />');
	$doc->addCustomTag('<meta property="og:title" content="'.MethodsForXml::XmlEncode($opengraphTitle).'" />');
	$doc->addCustomTag('<meta property="og:image" content="'.$opengraphImage.'" />');
	$doc->addCustomTag('<meta property="og:description" content="'.MethodsForXml::XmlEncode($opengraphDesc).'" />');
	$doc->addCustomTag('<meta property="og:type" content="article" />');

	$doc->addCustomTag('<meta property="article:tag" content="HTML5FlippingBook" />');
	$doc->addCustomTag('<meta property="article:author" content="'.$opengraphAuthor.'" />');

	$doc->addCustomTag('<meta property="twitter:title" content="'.MethodsForXml::XmlEncode($opengraphTitle).'" />');
	$doc->addCustomTag('<meta property="twitter:description" content="'.MethodsForXml::XmlEncode($opengraphDesc).'" />');
}

if ( !empty($this->item->custom_metatags) )
{
	foreach ( $this->item->custom_metatags as $custom_tag_name => $custom_tag_value )
	{
		$doc->addCustomTag('<meta property="'.$custom_tag_name.'" content="'.$custom_tag_value.'" />');
	}
}

$doc->addCustomTag('<meta name="apple-mobile-web-app-capable" content="yes" />');
$doc->addCustomTag('<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />');

$lang = JFactory::getLanguage();
$tag = $lang->getTag();
$tag = str_replace("-", "_", $tag);

?>

<?php if ($this->config->social_facebook_use == 1) {?>

<!--	<div id="fb-root"></div>
	<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/<?php /*echo $tag;*/?>/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>-->
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/<?php echo $tag;?>/sdk.js#xfbml=1&version=v3.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk-joomplace'));</script>
<?php  }

JFactory::getDocument()->addScriptDeclaration('ht5popupWindow = function (a, b, c, d, f) { window.open(a, b, "height=" + d + ",width=" + c + ",top=" + (screen.height - d) / 2 + ",left=" + (screen.width - c) / 2 + ",scrollbars=" + f + ",resizable").window.focus() };');

$html = array();

$html[] = '<form action="' . JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', false, (int)$uri->isSSL()) . '" id="adminForm" name="adminForm">';
$html[] = '<div id="html5flippingbook">';

if ($this->showListTitle)
{
	$html[] = '<div class="blog">';
	$html[] = 	'<h1>' . (empty($this->listTitle) ? JText::_('COM_HTML5FLIPPINGBOOK_FE_PUBLICATIONS_LIST') : $this->listTitle) . '</h1>';
	$html[] = '</div>';
}

$numPublicationDisplayed = 0;

$html[] = '<div class="html5fb-list container-fluid">';

for ($i = 0; $i < count($this->items);)
{
	$html[] = '<div class="row-fluid">';

	for ($j = $i; $j - $i < $colNumb; $j++) {

	    if(empty($this->items[$j])){
            continue;
        }
		$item = $this->items[$j];
		if (empty($item->c_id)) continue;
		
		// Checking access.

		$previewAccessGranted = $this->user->authorise('core.preview', COMPONENT_OPTION.'.publication.'.$item->c_id);
		$viewAccessGranted = $this->user->authorise('core.view', COMPONENT_OPTION.'.publication.'.$item->c_id);

		if (!$previewAccessGranted) continue;

		$numPublicationDisplayed += 1;

		// Preparing links and popups properties.
		
		$popupWidth = $item->width * 2+66;
		$popupHeight = $item->height+100;

		$linkTitle = JText::_('COM_HTML5FLIPPINGBOOK_FE_VIEW_PUBLICATION');
		
		$rawPublicationLink= 'index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$item->c_id;

		if ($isMobile)
		{
			$publicationLink = JRoute::_($rawPublicationLink.'&layout=mobile&tmpl=component', false, (int)$uri->isSSL());
			$viewPublicationLink= '<a href="'.$publicationLink.'" target="_blank" target="_self">';
			$viewPublicationLinkWithTitle = '<a href="'.$publicationLink.'" target="_blank" target="_self" title="'.$linkTitle .'">';
		}
		elseif ($isTablet)
		{
			$publicationLink = JRoute::_($rawPublicationLink.'&tmpl=component', false, (int)$uri->isSSL());
			$viewPublicationLink= '<a href="'.$publicationLink.'" target="_blank">';
			$viewPublicationLinkWithTitle = '<a class="thumbnail" href="'.$publicationLink.'" target="_blank" title="'.$linkTitle .'">';
		}
		elseif ($item->c_popup == PublicationDisplayMode::DirectLink)
		{
			//$publicationLink = JRoute::_($rawPublicationLink, false, -1);
	        $publicationLink = JRoute::_($rawPublicationLink, false, (int)$uri->isSSL());
			$viewPublicationLink= '<a href="'.$publicationLink.'" target="_blank" target="_self">';
			$viewPublicationLinkWithTitle = '<a href="'.$publicationLink.'" target="_blank" target="_self" title="'.$linkTitle .'">';
		}
		else if ($item->c_popup == PublicationDisplayMode::DirectLinkNoTmpl)
		{
			$publicationLink = JRoute::_($rawPublicationLink.'&tmpl=component', false, (int)$uri->isSSL());
			$viewPublicationLink= '<a href="'.$publicationLink.'" target="_blank">';
			$viewPublicationLinkWithTitle = '<a href="'.$publicationLink.'" target="_blank" title="'.$linkTitle .'">';
		}
		else if ($item->c_popup == PublicationDisplayMode::PopupWindow)
		{
			$publicationLink = JRoute::_($rawPublicationLink.'&tmpl=component', false, (int)$uri->isSSL());
			$viewPublicationLink= '<a href="javascript: ht5popupWindow(\''.$publicationLink.'\', \'fm_'.$item->c_id.'\', '.$popupWidth.', '.$popupHeight.', \'no\');">';
			$viewPublicationLinkWithTitle = '<a href="javascript: ht5popupWindow(\''.$publicationLink.'\', \''.$item->c_id.'\', '.$popupWidth.', '.$popupHeight.', \'no\');" title="'.$linkTitle .'">';
		}
		else if ($item->c_popup == PublicationDisplayMode::ModalWindow)
		{
			$publicationLink = JRoute::_($rawPublicationLink."&tmpl=component", false, (int)$uri->isSSL());
			$viewPublicationLink= '<a class="html5-modal" rel="{handler: \'iframe\', size: {x: '.$popupWidth.', y:'.$popupHeight.'}}" href="'.$publicationLink.'">';
			$viewPublicationLinkWithTitle = '<a class="html5-modal" rel="{handler: \'iframe\', size: {x: '.$popupWidth.', y:'.$popupHeight.'}}" href="'.$publicationLink.'" title="'.$linkTitle .'">';
		}

		// Preparing Publication's thumbnail.
		
		$thumbnailPath = COMPONENT_MEDIA_PATH.'/thumbs/'.$item->c_thumb;
		
		if ($item->c_thumb == "" || !is_file($thumbnailPath))
		{
			$thumbnailUrl = COMPONENT_IMAGES_URL."no_image.png";
		}
		else
		{
			$thumbnailUrl = COMPONENT_MEDIA_URL."thumbs/".$item->c_thumb;
		}
		
		// Output.
		
		$html[] = '<div class="html5fb-list-item span' . 12/$colNumb . '">';
		
		$html[] = 	'<div class="html5fb-pict">';
		if ($viewAccessGranted) $html[] = $viewPublicationLinkWithTitle;
		$html[] = 			'<img class="html5fb-img" src="' . $thumbnailUrl . '" alt="' . htmlspecialchars($item->c_title) . '" />';
		if ($viewAccessGranted) $html[] = '</a>';
		$html[] = 	'</div>';
		
		$html[] = 	'<div class="html5fb-descr">';
		
		$html[] = 		'<h2 class="html5fb-name">';
		if ($viewAccessGranted) $html[] = $viewPublicationLinkWithTitle;
		$html[] = 			htmlspecialchars($item->c_title);
		if ($viewAccessGranted) $html[] = '</a>';
		$html[] = 		'</h2>';
		
		if (strlen($item->c_pub_descr) != "")
		{
			$html[] = '<p>';
			
			if (strlen($item->c_pub_descr) <= 990)
			{
				$html[] = $item->c_pub_descr;
			}
			else
			{
				$html[] = substr($item->c_pub_descr, 0, strpos($item->c_pub_descr, ' ', 990)).' ...';
			}
			
			$html[] = '</p>';
		}
		
		if ($viewAccessGranted)
		{
			$html[] = 		'<div class="html5fb-links">';
			$html[] = 			$viewPublicationLink;
			$html[] = 				htmlspecialchars( (empty($this->viewPublicationButtonText) ? JText::_('COM_HTML5FLIPPINGBOOK_FE_VIEW_PUBLICATION') : $this->viewPublicationButtonText) );
			$html[] = 			'</a>';
		}
		else
		{
			$html[] = '<div class="html5fb-noaccess">';
			
			if ($this->user->id == 0)
			{
				$returnUrl = $_SERVER["REQUEST_URI"];
				
				$html[] = 	JText::_('COM_HTML5FLIPPINGBOOK_FE_SHOULD_LOGIN') . '.' . '&nbsp;';
				$html[] = 	'<a href="' . JRoute::_('index.php?option=com_users&view=login&Itemid='.COMPONENT_ITEM_ID.'&return=' . base64_encode($returnUrl), false, (int)$uri->isSSL()) . '">';
				$html[] = 		JText::_('COM_HTML5FLIPPINGBOOK_FE_LOGIN_NOW');
				$html[] = 	'</a>';
			}
			else
			{
				$html[] = 	JText::_('COM_HTML5FLIPPINGBOOK_FE_NO_RIGHTS') . '.';
			}
		}

	    if ($item->c_enable_pdf) {

	        $pdfLink = JURI::root().'index.php?option='.COMPONENT_OPTION
	            .'&task=getpdf'
	            .'&id='.$item->c_id
	            .'&filename='.preg_replace('/[<>:"\/\\\|\?\*]/is', '', $item->c_background_pdf);

	        $html[] =   '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
	        $html[] =   '<a href="'.$pdfLink.'" class="icon_pdf">'.JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_PDF').'</a>';
	        }
		
		$html[] = 		'</div>';
		
		if ($item->c_show_cdate)
		{
			//$date = new JDate($item->c_created_time);
			//$date = $date->toUnix();
			//$dateString = gmdate("Y-m-d", $date);
			//$html[] = '<div class="html5fb-date">' . $dateString . '</div>';
            $html[] = '<div class="html5fb-date">' . JHtml::_('date', $item->c_created_time, JText::_('DATE_FORMAT_LC4')) . '</div>';
		}

	    if ($viewAccessGranted)
		{
			//==================================================
			// Social intergation.
			//==================================================

			if ($this->config->social_twitter_use == 1 ||
				$this->config->social_linkedin_use == 1 ||
				$this->config->social_facebook_use == 1)
			{
				$html[] = '<div class="html5fb-social">';
				
				$pageLink = JUri::base().JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$item->c_id.'&Itemid='.COMPONENT_ITEM_ID, false, (int)$uri->isSSL());

				if ($this->config->social_twitter_use == 1)
				{
                    $html[] = '<div class="html5fb-social-btn">' .
                        '<a href="https://twitter.com/intent/tweet?text='.str_replace(array('?', '&', '&amp;'), '', $item->c_title) .'%20'.urlencode(JUri::root().substr($pageLink, 1)).'" ' .
                        ' class="twitter-share-button" ' .
                        ' data-url="' . urlencode(JUri::root().substr($pageLink, 1)) . '"' .
                        ' data-size="' . $this->config->social_twitter_size . '"' .
                        '>Tweet</a>' .
                        '</div>';
				}
				
				if ($this->config->social_linkedin_use == 1)
				{
					$html[] = '<div class="html5fb-social-btn">' .
							'<script type="IN/Share"' .
							' data-url="' . urlencode(JUri::root().substr($pageLink, 1)) . '"' .
							' data-counter="' . $this->config->social_linkedin_annotation . '"' .
							'></script>' .
						'</div>';
				}

				if ($this->config->social_facebook_use == 1)
				{
					$html[] = '<div class="html5fb-social-btn">' .
							'<div class="fb-like" data-show-faces="false" data-width="50" data-colorscheme="light" data-share="false" ' .
							' data-action="' . $this->config->social_facebook_verb . '"' .
							' data-layout="' . $this->config->social_facebook_layout . '"' .
                            ' data-href="' . urlencode(JUri::root().substr($pageLink, 1)) . '"' .
							'></div>' .
						'</div>';
				}
				
				$html[] = '</div><div style="clear: both;"><br clear="all"></div>';
			}
		}
		
		$html[] = 	'</div>';
		$html[] = '</div>';
	}
	$html[] = '</div>';
	$i = $j;
}

$html[] = '</div>';

if ($numPublicationDisplayed == 0)
{
	$html[] = '<h3>' . JText::_('COM_HTML5FLIPPINGBOOK_FE_CATEGORY_NO_ITEMS') . '</h3>';
}

$html[] = '</div>';

$html[] = '<div class="html5fb pagination">';
$html[] =     '<div class="btn-group pull-right">';
$html[] =         '<label for="limit" class="element-invisible">';
$html[] =             JText::_('JGLOBAL_DISPLAY_NUM');
$html[] =         '</label>';
$html[] =         $this->pagination->getLimitBox();
$html[] =     '</div>';
$html[] =     $this->pagination->getPagesLinks();
$html[] = '</div>';
$html[] = '<input type="hidden" name="option" value="com_html5flippingbook" />';
$html[] = '<input type="hidden" name="view" value="html5flippingbook" />';
$html[] = '<input type="hidden" name="Itemid" value="' . $jinput->get('Itemid', 0, 'INT') . '"/>';
$html[] = '</form>';

echo implode("\r\n", $html);
?>

<?php if ($this->config->social_twitter_use == 1) { ?>
	
	<script type="text/javascript">
	(function() {
        var twitterScriptTag = document.createElement('script');
        twitterScriptTag.type = 'text/javascript';
        twitterScriptTag.async = true;
        twitterScriptTag.src = 'http://platform.twitter.com/widgets.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(twitterScriptTag, s);
        })();
	</script>
	
<?php } ?>

<?php if ($this->config->social_linkedin_use == 1) { ?>
	
	<script type="text/javascript" src="//platform.linkedin.com/in.js">lang:<?php echo $tag?></script>
	
<?php } ?>
