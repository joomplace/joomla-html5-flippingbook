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
//JHtml::_('bootstrap.loadCss');

$uri = JUri::getInstance();
$jinput = JFactory::$application->input;

require_once (COMPONENT_LIBS_PATH . 'Mobile_Detect.php');
$detectMobile = new Mobile_Detect_HTML5FB();

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

	$pageLink = JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id, FALSE, $uri->isSSL());

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

	$doc = JFactory::getDocument();

	if ( !empty($this->config->social_facebook_og_app_id) )
	{
		$doc->addCustomTag('<meta property="fb:admins" content="'.$this->config->social_facebook_og_admin_id.'" />');
		$doc->addCustomTag('<meta property="fb:app_id" content="'.$this->config->social_facebook_og_app_id.'" />');
	}

	$doc->addCustomTag('<meta property="og:url" content="'.$pageLink.'" />');
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

$lang = JFactory::getLanguage();
$tag = $lang->getTag();
$tag = str_replace("-", "_", $tag);

?>

<?php if ($this->config->social_facebook_use == 1) {?>

	<div id="fb-root"></div>
	<script type="text/javascript">(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/<?php echo $tag;?>/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk-joomplace'));</script>

<?php  }

JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_READING_TIP');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READING');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_FAVORITE_TIP');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_FAVORITE');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_READ_TIP');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READ');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_USER');
JText::script('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_ACTION');

$user = JFactory::getUser();
$doc  = JFactory::getDocument();

$doc->addScriptDeclaration('ht5popupWindow = function (a, b, c, d, f) { window.open(a, b, "height=" + d + ",width=" + c + ",top=" + (screen.height - d) / 2 + ",left=" + (screen.width - c) / 2 + ",scrollbars=" + f + ",resizable").window.focus() };');
$doc->addScriptDeclaration("var user = " . $user->get('id') . ";");
$doc->addScript(COMPONENT_JS_URL . 'category.min.js');
//$doc->addScriptDeclaration("jQuery(document).ready(function() {jQuery('#html5flippingbook .modal').attr('rel', \"{handler: 'iframe', size: {x: jQuery(window).width(), y: jQuery(window).height()}}\");});");

$html = array();
$html[] = '<form action="' . JRoute::_('index.php?option=com_html5flippingbook&view=html5flippingbook', FALSE, $uri->isSSL()) . '" id="adminForm" name="adminForm">';
$html[] = '<div id="html5flippingbook">';

if ($this->showListTitle)
{
	$html[] = '<div class="blog">';
	$html[] = 	'<h1 class="pull-left">' . (empty($this->listTitle) ? JText::_('COM_HTML5FLIPPINGBOOK_FE_PUBLICATIONS_LIST') : $this->listTitle) . '</h1>';
	$html[] =   ($user->get('id') ? '<a href="' . JRoute::_('index.php?option=com_html5flippingbook&view=profile', FALSE, $uri->isSSL()) . '" class="pull-right profile-link hasTooltip" title="'.JText::_('COM_HTML5FLIPPINGBOOK_FE_PROFILE_LINK').'"><span class="fa fa-user fa-2x"></span></a>' : '');
	$html[] = '</div>';

	$html[] = '<br clear="all"/>';
}

$numPublicationDisplayed = 0;

$html[] = '<ul class="html5fb-list">';

$downloadOptionAccess = $this->user->authorise('core.download', COMPONENT_OPTION);

foreach ($this->items as $i => $item)
{

	if (empty($item->c_id)) continue;
	
	// Checking access.
	$previewAccessGranted = $this->user->authorise('core.preview', COMPONENT_OPTION.'.publication.'.$item->c_id);
	$viewAccessGranted = $this->user->authorise('core.view', COMPONENT_OPTION.'.publication.'.$item->c_id);
	$downloadOptionAccessGranted = $this->user->authorise('core.download', COMPONENT_OPTION.'.publication.'.$item->c_id);

	if (!$previewAccessGranted) continue;

	$numPublicationDisplayed += 1;

	// Preparing links and popups properties.
	$data = HTML5FlippingBookFrontHelper::htmlPublHelper($isMobile, $isTablet, $item);

	// Output.
	$html[] = '<li class="html5fb-list-item">';
	
	$html[] = 	'<div class="html5fb-pict">';
	$html[] =       '<div class="loader" id="loader-' . $item->c_id . '" style="display: none;">';
	$html[] =           '<img src="' . COMPONENT_IMAGES_URL . 'progress.gif" alt="loading animation"/>';
	$html[] =       '</div>';

	if ($viewAccessGranted) $html[] = $data->viewPublicationLinkWithTitle;
	$html[] = 			'<img class="html5fb-img" src="' . $data->thumbnailUrl . '" alt="' . htmlspecialchars($item->c_title) . '" />';
	if ($viewAccessGranted) $html[] = '</a>';
	$html[] = 	'</div>';
	
	$html[] = 	'<div class="html5fb-descr">';
	$html[] =       '<div class="pull-left">';
	$html[] = 		    '<h2 class="html5fb-name">';
	if ($viewAccessGranted) $html[] = str_replace("thumbnail", "", $data->viewPublicationLinkWithTitle);
	$html[] = 			htmlspecialchars($item->c_title);
	if ($viewAccessGranted) $html[] = '</a>';
	$html[] = 		    '</h2>';
	$html[] =       '</div>';

	if ($user->get('id'))
	{
		$html[] = '<div class="btn-group pull-right">';
		$html[] =   '<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">';
		$html[] =       '<span class="fa fa-gear"></span>';
		$html[] =       '<span class="caret"></span>';
		$html[] =   '</a>';
		$html[] =   '<ul class="dropdown-menu">';
		$html[] =       '<li>';

		if (isset($item->uid) && $user->get('id') == $item->uid && $item->read_list == 1)
		{
			$html[] =           '<a href="javascript: void(0);" id="reading_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'reading_remove\'); return false;">';
			$html[] =               '<i class="fa fa-trash-o"></i> <span id="reading_text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READING') . '</span>';
			$html[] =           '</a>';
		}
		else
		{
			$html[] =           '<a href="javascript: void(0);" id="reading_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'reading\'); return false;">';
			$html[] =               '<i class="fa fa-list"></i> <span id="reading_text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_READING_TIP') . '</span>';
			$html[] =           '</a>';
		}

		$html[] =       '</li>';
		$html[] =       '<li>';

		if (isset($item->uid) && $user->get('id') == $item->uid && $item->fav_list == 1)
		{
			$html[] =           '<a href="javascript: void(0);" id="favorite_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'favorite_remove\'); return false;">';
			$html[] =               '<i class="fa fa-trash-o"></i> <span id="favorite_text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_FAVORITE') . '</span>';
			$html[] =           '</a>';
		}
		else
		{
			$html[] =           '<a href="javascript: void(0);" id="favorite_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'favorite\'); return false;">';
			$html[] =               '<i class="fa fa-star"></i> <span id="favorite_text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_FAVORITE_TIP') . '</span>';
			$html[] =           '</a>';
		}

		$html[] =       '</li>';
		$html[] =       '<li>';

		if (isset($item->uid) && $user->get('id') == $item->uid && $item->read == 1)
		{
			$html[] =           '<a href="javascript: void(0);" id="read_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'read_remove\'); return false;">';
			$html[] =               '<i class="fa fa-eye"></i> <span id="read_text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READ') . '</span>';
			$html[] =           '</a>';
		}
		else
		{
			$html[] =           '<a href="javascript: void(0);" id="read_' . $item->c_id . '" onclick="userPublAction(' . $item->c_id . ', \'read\'); return false;">';
			$html[] =               '<i class="fa fa-eye-slash"></i> <span id="read_text_' . $item->c_id . '">' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ACTION_READ_TIP') . '</span>';
			$html[] =           '</a>';
		}

		$html[] =       '</li>';
		$html[] =   '</ul>';
		$html[] = '</div>';
	}

	$html[] =       '<br clear="all" />';
	
	if($item->introtext){
        $html[] = '<p>'.JHtml::_('content.prepare', $item->introtext)'</p>';
    }
	
	if ($viewAccessGranted)
	{
		$html[] = 		'<div class="html5fb-links">';
		$html[] = 			$data->viewPublicationLink;
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
			$html[] = 	'<a href="' . JRoute::_('index.php?option=com_users&view=login&Itemid='.COMPONENT_ITEM_ID.'&return=' . base64_encode($returnUrl), FALSE, $uri->isSSL()) . '">';
			$html[] = 		JText::_('COM_HTML5FLIPPINGBOOK_FE_LOGIN_NOW');
			$html[] = 	'</a>';
		}
		else
		{
			$html[] = 	JText::_('COM_HTML5FLIPPINGBOOK_FE_NO_RIGHTS') . '.';
		}
	}

	if ($downloadOptionAccess && $downloadOptionAccessGranted)
	{
		$downloadList = HTML5FlippingBookFrontHelper::generateDownloadOptions($item->c_id);
		if ($downloadList)
		{
			$html[] =   '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
			$html[] =   JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_OPTIONS') . $downloadList;
		}
	}

	$html[] = 		'</div>';
	
	if ($item->c_show_cdate)
	{
		$date = new JDate($item->c_created_time);
		$date = $date->toUnix();
		$dateString = gmdate("Y-m-d", $date);
		
		$html[] = 	'<div class="html5fb-date">' . $dateString . '</div>';
	}

    if ($viewAccessGranted)
	{
		//==================================================
		// Social intergation.
		//==================================================

		if ($this->config->social_google_plus_use == 1 ||
			$this->config->social_twitter_use == 1 ||
			$this->config->social_linkedin_use == 1 ||
			$this->config->social_facebook_use == 1)
		{
			$html[] = '<div class="html5fb-social">';
			
			$pageLink = JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$item->c_id.'&Itemid='.COMPONENT_ITEM_ID, FALSE, $uri->isSSL());

			if ($this->config->social_google_plus_use == 1)
			{
				$html[] = '<div class="html5fb-social-btn">' .
						'<div class="g-plusone" data-width="70"' .
						' data-size="' . $this->config->social_google_plus_size . '"' .
						' data-annotation="' . $this->config->social_google_plus_annotation . '"' .
						' href="' . $pageLink . '"' .
						'></div>' .
					'</div>';
			}
			
			if ($this->config->social_twitter_use == 1)
			{
				$html[] = '<div class="html5fb-social-btn">' .
						'<a href="http://twitter.com/share" class="twitter-share-button"' .
						' data-url="' . $pageLink . '"'.
						' data-size="' . $this->config->social_twitter_size . '"' .
						' data-count="' . $this->config->social_twitter_annotation . '"' .
						' data-lang="' . $this->config->social_twitter_language . '"' .
						'>Tweet</a>' .
					'</div>';
			}
			
			if ($this->config->social_linkedin_use == 1)
			{
				$html[] = '<div class="html5fb-social-btn">' .
						'<script type="IN/Share"' .
						' data-url="' . $pageLink . '"' .
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
						' data-href="' . $pageLink . '"' .
						'></div>' .
					'</div>';
			}
			
			$html[] = '</div><div style="clear: both;"><br clear="all"></div>';
		}
	}
	
	$html[] = 	'</div>';
	
	$html[] = '</li>';
}

$html[] = '</ul>';

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

<?php if ($this->config->social_google_plus_use == 1) { ?>
	
	<script type="text/javascript">
	window.___gcfg = {lang: '<?php echo $this->config->social_google_plus_language; ?>'};
	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
	</script>
	
<?php } ?>

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
	
	<script type="text/javascript" src="//platform.linkedin.com/in.js"></script>
	
<?php } ?>
