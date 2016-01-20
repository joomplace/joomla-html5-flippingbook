<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//----------------------------------------------------------------------------------------------------
$uri = JUri::getInstance();

require_once (JPATH_SITE . '/components/com_html5flippingbook/libs/Mobile_Detect.php');
$detectMobile = new Mobile_Detect_HTML5FB();

// Exclude tablets.
if ($detectMobile->isMobile() && !$detectMobile->isTablet())
{
    JFactory::getApplication()->redirect(JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id.'&layout=mobile&tmpl=component', false, $uri->isSSL()));
    return true;
}

function rightToLeftDeff($width = false, $height = false, $item)
{
    $debug_backtrace = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
    switch ($debug_backtrace[1]['function'])
    {
        case 'addAdditionStylesDeclaration':
			if ( $item->template->display_nextprev )
			{
				if ( !$item->right_to_left )
				{
					// next and prev buttons style for ltr direction
					return array(
    					'.next-button { background-position:-38px 50%; }' ,
    					'.previous-button { background-position:-4px 50%; }' ,
    					'.next-button { display: block; }'
					);
				}
				else
				{
					// next and prev buttons style for rtr direction
					return array(
						'.next-button { background-position:-4px 50%; }' ,
						'.previous-button { background-position:-38px 50%; }' ,
						'.previous-button { display: block; }'
					);
				}
			}
			else
				return array();
        break;
        case 'addFlipRunningScripts':
            if ( !$item->right_to_left )
            {
                // turn page checking
                $result = array(
                    'slider_one_page_left' => ( $width < 1000 ? round(($width/2)/1.2+10) : round($width/2) ),
                    'slider_tho_page_left' => round(($width/2)/2.2),
                    'turningSection' =>"
                                    pages = $(this).turn('pages');
                                        if ( page > 1 )           {  $('.previous-button').show(); }
                                            else                  {  $('.previous-button').hide(); }

                                        if ( page != pages )
                                        {
                                            if ( page == (pages-1) && pages%2!=0 )
                                            {	$('.next-button').hide();	}
                                            else
                                            {	$('.next-button').show();	}
                                        }
                                        else
                                        {	$('.next-button').hide();	}
                                    ",
                // bind next and prev buttons onclick
                'bindingSection' => "$('.next-button').bind('click', function() {  $('.flipbook').turn('next'); });
                                     $('.previous-button').bind('click', function() {  $('.flipbook').turn('previous'); });"
                );
            } else
            {
                $result = array(
                    'slider_one_page_left' => ( $width < 1000 ? round($width/2/6*-1) : 0 ),
                    'slider_tho_page_left' => round(($width/2)/2.2),
                    'turningSection' =>"
                                        pages = $(this).turn('pages');
                                        if ( page > 1 )       {  $('.next-button').show();		}
                                            else              {  $('.next-button').hide();		}

                                        if ( page != pages )  {  $('.previous-button').show();	}
                                            else              {  $('.previous-button').hide();	}
                                    ",
                // bind next and prev buttons onclick
                'bindingSection' => "$('.previous-button').bind('click', function() {  $('.flipbook').turn('next'); });
                                     $('.next-button').bind('click', function() {  $('.flipbook').turn('previous'); });"
                );;
            }


			if ( !$item->template->display_nextprev )
			{
				$result['turningSection'] = '';
				$result['bindingSection'] = '';
			}


			return $result;
        break;
    }
}

//----------------------------------------------------------------------------------------------------
function addAdditionStylesDeclaration($width, $height, $item)
{
    $resultStyles = array();
    $detectMobile = new Mobile_Detect_HTML5FB();

	$marginTop = $height;
	$marginLeft = $width;

    if ( $item->template->display_nextprev )
	{
		$resultStyles[]= '.next-button, .previous-button{ height: 100%; }';
	}
    if ( $item->template->display_slider || $item->template->display_pagebox )
	{
        if (!$detectMobile->isTablet())
        {
            $resultStyles[] = '#slider-bar, #page-bar { top:'.($height+10).'px; }';
        }
		$resultStyles[] = '.fb_slider { width: '.round( $width / 1.5 ).'px; }';
		$resultStyles[] = '#slider { width: '.(round( $width / 1.5 )-80).'px; }';
	}

	$resultStyles[]= '.flipbook-viewport { height:'.($height+60).'px; }';

    $resultStyles = array_merge($resultStyles, rightToLeftDeff(false, $height, $item ) );

    // top, left positions bug fix
    if (!$detectMobile->isTablet())
    {
        if ($height > 600)
        {
            $resultStyles[]= '.flipbook-viewport .container { margin-left: -'.round($marginLeft/2).'px; margin-top: -'.round($marginTop/2).'px }';
        }
        else
        {
            $resultStyles[]= '.flipbook-viewport .container {  margin-left: -'.round($marginLeft/2).'px; margin-top: -'.round($marginTop/2).'px }';
        }
    }

	JFactory::getDocument()->addStyleDeclaration( trim( implode(" ", $resultStyles) ) );
}

//----------------------------------------------------------------------------------------------------
function addFlipRunningScripts($width, $height, $item, $isTmplComp)
{
    $rightLeftDeff = rightToLeftDeff($width, $height, $item);

    $detectMobile = new Mobile_Detect_HTML5FB();

	$img = array();
	foreach ($item->pages as $i => $page) {
		if (isset($page['page_image']) && !empty($page['page_image']))
		{
			$smallIMG = COMPONENT_MEDIA_URL. 'images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : '') . $page['page_image'];
			$largeIMG = COMPONENT_MEDIA_URL. 'images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : '') . 'original' . '/' . str_replace(array('th_', 'thumb_'), '', $page['page_image']);
			if (!file_exists(COMPONENT_MEDIA_PATH . '/images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : '') . 'original' . '/' . str_replace(array('th_', 'thumb_'), '', $page['page_image'])))
			{
				$largeIMG = $smallIMG;
			}

			$pageN = $i + 1;
			$img[$pageN] = array(
				'small' => $smallIMG,
				'large' => $largeIMG
			);
		}
	}

    ob_end_flush();
    ob_start();
    ?>
    <script type="text/javascript">

        var rawPublicationLink= "<?php echo $item->rawPublicationLink; ?>";
        var rawPublicationTitle = "<?php echo $item->c_title; ?>";
        var sliderThumbs = <?php echo (int)$item->template->slider_thumbs; ?>;
        var previewSrc = "<?php echo COMPONENT_MEDIA_URL; ?>thumbs/preview_<?php echo $item->c_id; ?>.gif?<?php echo time();?>";
        var naviSettings = <?php echo $item->navi_settings;?>;
        var cntPages = <?php echo count($item->pages);?>;
        var isModal = <?php echo $item->c_popup;?>;
        var isTablet = <?php echo ($detectMobile->isTablet() ? 1 : 0);?>;
        var clickGoTo = false;
        var isHard = false;
        var $mainContainer = $('#mainFlipBookDiv');
        <?php if (count($img)): ?>
        var imgLink = <?php echo json_encode($img);?>;
        <?php endif;?>

        <?php if (!$isTmplComp):?>
        jQuery(document).ready(function() {
            jQuery('body').prepend('<div class="load-overlay"><img class="load-image" src="<?php echo COMPONENT_IMAGES_URL; ?>progress.gif" alt=""></div>');
        });
        <?php endif;?>

        function loadApp() {

	        var $flipbook = $('.flipbook');

            <?php if ($item->template->display_slider && !$detectMobile->isTablet()) { ?>
            $('#slider-bar').css('left', '<?php echo $rightLeftDeff['slider_one_page_left']; ?>px');
            <?php } ?>

            $('#fb_bookname').hide();
            $('#page-bar').hide();

            if ($flipbook.width()==0 || $flipbook.height()==0) {
                setTimeout(loadApp, 10);
                return;
            }

			Hash.on('^page\/([0-9]*)$', {
				yep: function(path, parts) {
					var page = parts[1];
					if (page!==undefined) {
						if ($flipbook.turn('is')) {
                            if (naviSettings == 0) {
                                page = parseInt(page) + 2;
                            }
                            
                            $flipbook.turn('page', page);
                        }
					}
				},
				nop: function(path) {
					if ($flipbook.turn('is')) {
                        $flipbook.turn('page', 1);
                    }
				}
			});

            $flipbook.turn({
                    width: <?php echo $width;?>,
                    height: <?php echo $height;?>,
                    elevation: 50,
                    gradients: true,
                    autoCenter: true,
                    acceleration: !isChrome(),
                    direction: '<?php echo $item->direction; ?>',
                when: {
                    turning: function(e, page, view) {
                        <?php echo $rightLeftDeff['turningSection']; ?>

                        fbTurningPage($flipbook, page, $(this).turn('pages'), <?php echo $rightLeftDeff['slider_one_page_left']; ?>, <?php echo $rightLeftDeff['slider_tho_page_left']; ?>, isTablet);

                        if (naviSettings == 0) {
                            page = parseInt(page) - 2;

                            if (page < 1 || page > (cntPages - 4)) {
                                page = 0;
                            }
                        }

                        Hash.go(page == 0 ? '' : 'page/' + page).update();
                    },

                    turned: function(e, page, view) {
						<?php if ( $item->template->display_slider ) { ?>
                        	$('#slider').slider('value', getViewNumber($(this), page));
                        <?php } ?>
                        $(this).turn('center');
                    },

                    start: function(e, pageObj) {
						<?php if ( $item->template->display_slider ) { ?>
                        	moveBar(true);
                        <?php } ?>
                    },

                    end: function(e, pageObj) {
						<?php if ( $item->template->display_slider ) { ?>
							var book = $(this);
							setTimeout(function() {
								$('#slider').slider('value', getViewNumber(book));
							}, 1);
							moveBar(false);
                        <?php } ?>
                    }
                }
            });

	        /*Zoom option*/
	        /*Only for page, which contains only image, without any text*/
	        /*For html pages, see functions (zoomInText, zoomOutText) in html5fb.scripts.js*/
	        $mainContainer.zoom({
		        flipbook: $flipbook,
		        max: function () {
			        return 2.4;
		        },
		        when: {
			        resize: function (event, scale, page, pageElement) {
				        if (scale == 1) {
					        if (imgLink.hasOwnProperty(page) && imgLink[page].hasOwnProperty('small')) {
						        loadSmallPage(page, pageElement, imgLink[page].small);
					        }
				        }
				        else {
					        if (imgLink.hasOwnProperty(page) && imgLink[page].hasOwnProperty('large')) {
						        loadLargePage(page, pageElement, imgLink[page].large);
					        }
				        }
			        },

			        change: function (event, scale) {
				        if (scale == 1) {
					        $mainContainer.addClass('no-transition').height('');
					        $('body > :not(#mainFlipBookDiv)').show();
					        $('.tb_social, .bar, #slider-bar').css({visibility: 'visible'});
					        zoomOutButton(false);
				        } else {
					        $flipbook.removeClass('animated').addClass('zoom-in');
					        $mainContainer.addClass('no-transition').height($(window).height());
                            <?php if ($isTmplComp):?>
					        $('body > :not(#mainFlipBookDiv)').hide();
                            <?php endif;?>
				        }

			        },

			        zoomIn: function () {
				        $('.tb_social, .bar, #slider-bar').css({visibility: 'hidden'});
				        zoomOutButton(true);
			        },

			        zoomOut: function () {
				        setTimeout(function () {
					        $flipbook.addClass('animated').removeClass('zoom-in');
					        $(".load-overlay").fadeOut(1000);
				        }, 0);
			        },

			        swipeLeft: function () {
				        $flipbook.turn('next');
			        },

			        swipeRight: function () {
				        $flipbook.turn('previous');
			        }
		        }
	        });

	        $mainContainer.bind('zoom.doubleTap', zoomTo);

			<?php if ( $item->template->display_slider ) { ?>
            	setSliderMaxViews($flipbook);
            <?php } ?>
            $flipbook.addClass('animated');

            <?php echo $rightLeftDeff['bindingSection']; ?>

            if (isTablet) {
                $(window).resize(function() {
                    if (!clickGoTo) {
                        resizeViewport_Tablet();
                    }
                }).bind('orientationchange', function() {
                    if (!clickGoTo) {
                        resizeViewport_Tablet();
                    }
                });

                resizeViewport_Tablet();

                if ($flipbook.turn('page') == $flipbook.turn('pages') || $flipbook.turn('page') == 1) {
                    var $tabletBar = $('.tablet-bar');
                    var $tabletBottomBar = $('.tablet-bottomBar');

                    $tabletBar.hide();
                    $tabletBottomBar.hide();

                    if ($tabletBar.hasClass('open')) {
                        $tabletBar.css({height: 0, paddingTop: 0}).removeClass('open');
                        $tabletBottomBar.css({height: 0, paddingTop: 0});
                    }
                }

                $('.flipbook-viewport').on('click', function(e) {
                    clickGoTo = false;

                    if (e.target.id == 'goto_page_input') {
                        e.preventDefault();
                        clickGoTo = true;
                        return false;
                    }

                    var $tabletBar = $('.tablet-bar');
                    var $tabletBottomBar = $('.tablet-bottomBar');

                    if ($flipbook.turn('page') != 1 && $flipbook.turn('page') != $flipbook.turn('pages')) {
                        if ($tabletBar.hasClass('open')) {
                            $tabletBar.animate({height: 0, paddingTop: 0}, 300, 'swing', function() {$(this).hide();}).removeClass('open');
                            $tabletBottomBar.animate({height: 0, paddingTop: 0}, 200, 'swing', function() {$(this).hide();});
                            $flipbook.turn("disable", false);
                        }
                        else {
                            $flipbook.turn("disable", true);
                            $tabletBar.show().animate({height: 40, paddingTop: 5}, 300).addClass('open');
                            $tabletBottomBar.show().animate({height: 40, paddingTop: 5}, 200);
                        }
                    }
                });
            }

            $mainContainer.css({visibility: ''});
        }

        $mainContainer.css({visibility: 'hidden'});

        yepnope({
            test : Modernizr.csstransforms,
            yep: ['<?php echo COMPONENT_JS_URL;?>html5fb.min.js'],
            nope: ['<?php echo COMPONENT_JS_URL;?>html5fb.html4.min.js' <?php echo ( $item->template->display_slider ? ",'".COMPONENT_CSS_URL.'slider-html4.css'."'" : '');?>],
            <?php if ( $item->template->display_slider) { ?> both: ['<?php echo COMPONENT_CSS_URL.'slider.css?v11';?>'], <?php } ?>
            complete: loadApp
        });
    </script>
    <?php

    $Result = ob_get_contents();
    ob_clean();

   // echo $Result;
   	echo preg_replace('/[\s]+/is', ' ', $Result );
}

//----------------------------------------------------------------------------------------------------
function addFlipDiv($config, $item)
{
    $detectMobile = new Mobile_Detect_HTML5FB();
    ?>
    <div id="mainFlipBookDiv">
        <div class="flipbook-viewport" id="interesting">
            <div class="container" id="zoom-viewport">
                <div class="flipbook">
                    <div ignore="1" class="fb_topBar <?php echo $detectMobile->isTablet() ? 'tablet-bar' : '';?>">
            			<?php if ( $item->template->display_title ) { ?>
                            <h2 id="fb_bookname"><?php echo $item->c_title; ?></h2>
            			<?php } ?>

            			<?php if ( $item->template->display_topicons ) { ?>
                        <div class="tb_social">
                            <?php
							if ( !empty($item->contents_page) ) echo '<i class="tbicon table-contents" onclick="location.href=\''.$item->rawPublicationLink.'#page/'.$item->contents_page.'\'" title="'.JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_CONTENTS').'" rel="'.$item->contents_page.'"></i>';

                            echo '<i class="tbicon fullscreen-ico" id="fullscreen" title="' . JText::_('COM_HTML5FLIPPINGBOOK_FE_FULLSCREEN') . '"></i>';

                            echo '<i class="zoom-in-ico" title="' . JText::_('COM_HTML5FLIPPINGBOOK_FE_ZOOM_IN') . '"></i>';

							if ($item->c_enable_pdf) {
								$pdfLink = JURI::root().'index.php?option='.COMPONENT_OPTION . '&task=getpdf' . '&id='.$item->c_id . '&filename='.preg_replace('/[<>:"\/\\\|\?\*]/is', '', $item->c_background_pdf);
								echo '<i class="tbicon icon-pdf" title="'.JText::_('COM_HTML5FLIPPINGBOOK_BE_DOWNLOAD_PDF').'" onclick="location.href=\''.$pdfLink.'\'"></i>';
							}

                            if ( $config->social_facebook_use ) echo '<i class="tbicon share-facebook" title="'.JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_FACEBOOK').'"></i>';
                            if ( $config->social_twitter_use ) echo '<i class="tbicon share-twitter" title="'.JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_TWITTER').'"></i>';
                            if ( $config->social_google_plus_use ) echo '<i class="tbicon share-plus" title="'.JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_GOOGLEPLUS').'"></i>';
							?>
							<?php if($item->c_popup):?>
							<i class="tbicon icon-close" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_CLOSE'); ?>" onclick="window.close();"></i>
							<?php endif;?>
                        </div>
			            <?php } ?>
                    </div>

		<?php if ( $item->template->display_nextprev ) { ?>
                    <div ignore="1" class="next-button <?php if ( !$item->template->display_nextprev ) echo 'notvisible'; ?>"></div>
                    <div ignore="1" class="previous-button <?php if ( !$item->template->display_nextprev ) echo 'notvisible'; ?>"></div>
		<?php
				}

                    foreach ( $item->pages as $k => $page )
                    {
						$addition_style = ( !empty($page['page_hard']) ? 'class="hard"' : '');
                        if ( $page['c_enable_image'] )
                        {
                            echo '<div '.$addition_style.' style="background-image:url(\'' .COMPONENT_MEDIA_URL. 'images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : ''). $page['page_image'].'\'); background-size: cover;"></div>'."\n";
                        }
                        else
                        {
                            $page['c_text'] = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $page['c_text']);
                            $page['c_text'] = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $page['c_text']);
                            echo '<div '.$addition_style.'><div class="flipbook-page">'.$page['c_text'].'</div></div>'."\n";
                        }
                    }
                    ?>

                    <?php if ($detectMobile->isTablet()):?>
                        <div ignore="1" class="tablet-bottomBar">
                    <?php endif;?>

		<?php if ( $item->template->display_pagebox ) { ?>
                    <div ignore="1" id="page-bar">
						<label><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_GOTO_PAGE_TITLE'); ?></label>
						<input type="text" id="goto_page_input" value="" autocomplete="" placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_GOTO_PAGE'); ?>" />
						<span id="goto_page_input_button" onclick="$('.flipbook').turn('page',$('#goto_page_input').val());"></span>
                    </div>
		<?php } ?>

		<?php if ( $item->template->display_slider ) { ?>
                    <div ignore="1" id="slider-bar" class="fb_slider">
                        <div id="slider"></div>
                    </div>
		<?php } ?>
                    <?php if ($detectMobile->isTablet()):?>
                        </div>
                    <?php endif;?>

                </div>
            </div>
        </div>

    </div>
<?php
}

$FlipWidth = ($this->resolutions->width * 2);
$FlipHeight = $this->resolutions->height;

    $doc = JFactory::getDocument();

	if ( $this->item->opengraph_use )
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/libs/MethodsForXml.php');

		$pageLink = JUri::base().JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id, false, $uri->isSSL());

		$opengraphTitle = ($this->item->opengraph_title != '' ? $this->item->opengraph_title : $this->item->c_title);
		$opengraphAuthor = ($this->item->opengraph_author != '' ? $this->item->opengraph_author : $this->item->c_author);
		$imageFileName = ($this->item->opengraph_image != '' ? $this->item->opengraph_image : $this->item->c_thumb);
		$imageFullFileName = COMPONENT_MEDIA_PATH.'/thumbs/'.$imageFileName;

		if ($imageFileName != '' && is_file($imageFullFileName)) $opengraphImage = COMPONENT_MEDIA_URL.'thumbs/'.$imageFileName;
		else $opengraphImage = '';

		if ($this->item->opengraph_description != '')
			$opengraphDesc = $this->item->opengraph_description;
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
		$doc->addCustomTag('<meta property="og:image" content="'.str_replace(' ','%20',$opengraphImage).'" />');
		$doc->addCustomTag('<meta property="og:description" content="'.MethodsForXml::XmlEncode($opengraphDesc).'" />');
		$doc->addCustomTag('<meta property="og:updated_time" content="'.MethodsForXml::XmlEncode($this->item->c_created_time).'" />');
		$doc->addCustomTag('<meta property="og:type" content="article" />');

		$doc->addCustomTag('<meta property="article:tag" content="HTML5FlippingBook" />');
		//$doc->addCustomTag('<meta property="article:author" content="'.$opengraphAuthor.'" />');
		$doc->addCustomTag('<meta property="article:published_time" content="'.MethodsForXml::XmlEncode($this->item->c_created_time).'" />');

		$doc->addCustomTag('<meta property="twitter:title" content="'.MethodsForXml::XmlEncode($opengraphTitle).'" />');
		$doc->addCustomTag('<meta property="twitter:description" content="'.MethodsForXml::XmlEncode($opengraphDesc).'" />');
	}

	if ( !empty($this->item->custom_metatags) )
	{
		$doc = JFactory::getDocument();
		
		foreach ( $this->item->custom_metatags as $custom_tag_name => $custom_tag_value )
		{
			$doc->addCustomTag('<meta property="'.$custom_tag_name.'" content="'.$custom_tag_value.'" />');
			$doc->addCustomTag('<meta name="'.$custom_tag_name.'" content="'.$custom_tag_value.'" />');
		}
	}

    $doc->addCustomTag('<meta name="apple-mobile-web-app-capable" content="yes" />');
    $doc->addCustomTag('<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />');

JHtml::_('jquery.framework', false);

// addd viewpoint tag for mobile
JFactory::getDocument()->addCustomTag('<meta name="viewport" content="width = 1050, user-scalable = no" />');
JFactory::getDocument()->addScript(COMPONENT_JS_URL .'jquery-ui-1.10.4.custom.min.js');
JFactory::getDocument()->addScript(COMPONENT_JS_URL .'jquery.mousewheel.min.js');
JFactory::getDocument()->addScript(COMPONENT_JS_URL .'jquery-zoom.min.js');
JFactory::getDocument()->addScript(COMPONENT_JS_URL .'screenfull.min.js');
JFactory::getDocument()->addScript(COMPONENT_JS_URL .'html5fb.scripts.min.js');
JFactory::getDocument()->addStyleSheet(JURI::root().'index.php?option='.COMPONENT_OPTION.'&task=templatecss&template_id='.$this->item->c_template_id);
addAdditionStylesDeclaration( $FlipWidth, $FlipHeight, $this->item);

if ($this->tmplIsComponent)
{
	$lang = JFactory::$document->getLanguage();
	$direction = JFactory::$document->getDirection();
	@ob_clean();
	?>
	
<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <?php
	$hdata = JFactory::getDocument()->getHeadData();
	foreach ($hdata['scripts'] as $url => $params)
		if ( strpos($url, 'html5fb') === false && strpos($url, 'jquery') == false &&  strpos($url, 'screenfull') == false)
			unset( $hdata['scripts'][$url]);

	if ( !empty($hdata['custom']) )
		foreach ($hdata['custom'] as $k => $value )
			if ( strpos($value, '<script') !== false )
				unset($hdata['custom'][$k]);

	JFactory::getDocument()->setHeadData($hdata);

	echo JFactory::$document->loadRenderer("head")->fetchHead(JFactory::$document);
	?>
</head>
<link rel="icon" type="image/x-icon" href="/templates/<?php echo JFactory::getApplication()->getTemplate(); ?>/favicon.ico" />
<body style="overflow:hidden;">
	<div class="load-overlay">
		<img class="load-image" src="<?php echo COMPONENT_IMAGES_URL; ?>progress.gif" alt="">
	</div>
    <?php addFlipDiv($this->config, $this->item); ?>
    <?php addFlipRunningScripts( $FlipWidth, $FlipHeight, $this->item, TRUE); ?>
</body>
</html>
	
	<?php
	jexit();
}
else
{
	addFlipDiv($this->config, $this->item);
	addFlipRunningScripts( $FlipWidth, $FlipHeight, $this->item, FALSE);
}