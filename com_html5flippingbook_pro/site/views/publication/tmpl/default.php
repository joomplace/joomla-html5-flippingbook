<?php defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

require_once (COMPONENT_LIBS_PATH . 'Mobile_Detect.php');
$detectMobile = new Mobile_Detect_HTML5FB();
$uri = JUri::getInstance();

// Exclude tablets.
if ($detectMobile->isMobile() && !$detectMobile->isTablet())
{
	JFactory::getApplication()->redirect(JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id.'&layout=mobile&tmpl=component', FALSE, $uri->isSSL()));
	return true;
}

//----------------------------------------------------------------------------------------------------
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
						'.next-button { background-position: -34px 50%; }' ,
						'.previous-button { background-position: 0 50%; }' ,
						'.next-button { display: block; }'
					);
				}
				else
				{
					// next and prev buttons style for rtl direction
					return array(
						'.next-button { background-position: 0 50%; }' ,
						'.previous-button { background-position: -34px 50%; }' ,
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
                                    if (page > 1) {
                                        $('.previous-button').show();
                                    }
                                    else {
                                        $('.previous-button').hide();
                                    }

                                    if (page != pages) {
                                        if (page == (pages-1) && pages%2 != 0) {
                                            $('.next-button').hide();
                                        }
                                        else {
                                            $('.next-button').show();
                                        }
                                    }
                                    else {
                                        $('.next-button').hide();
                                    }
                                    ",
					// bind next and prev buttons onclick
					'bindingSection' => "$('.next-button').bind('click', function() {  $('.flipbook').turn('next'); });
                                     $('.previous-button').bind('click', function() {  $('.flipbook').turn('previous'); });"
				);
			}
			else
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
				);
			}


			if ( !$item->template->display_nextprev )
			{
				$result['turningSection'] = '';
				$result['bindingSection'] = '';
			}


			return $result;
			break;
	}

	return array();
}

//----------------------------------------------------------------------------------------------------
function addAdditionStylesDeclaration($width, $height, $item)
{
	$resultStyles = array();
	$detectMobile = new Mobile_Detect_HTML5FB();

	$marginTop = $height;
	$marginLeft = $width;

	if ($item->template->hard_cover)
	{
		JHtml::stylesheet(COMPONENT_CSS_URL . 'hardbook.css');

		$cntPages = count($item->pages) + 4;

		$resultStyles[] = '.html5-book .p' . ($cntPages % 2 == 0 ? $cntPages - 1 : $cntPages) . ', .html5-book .p' . ($cntPages % 2 == 0 ? $cntPages : $cntPages + 1) . ' { background-color:white; background-image:url("' . COMPONENT_IMAGES_URL . 'book-covers.jpg") !important;}';
		$resultStyles[] = '.html5-book .p' . ($cntPages % 2 == 0 ? $cntPages - 1 : $cntPages) . ' {background-position:-960px 0;}';
		$resultStyles[] = '.html5-book .p' . ($cntPages % 2 == 0 ? $cntPages : $cntPages + 1) . ' {background-position:-1440px 0;}';
		if (!$detectMobile->isTablet())
		{
			$resultStyles[] = '.html5-book .p2 { background-position: -480px 0 !important; }';
		}
	}
	else
	{
		JHtml::stylesheet(COMPONENT_CSS_URL . 'magazine.css');
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

	$resultStyles[]= '.flipbook-viewport { height:'.($height + 90).'px; }';

	$resultStyles = array_merge($resultStyles, rightToLeftDeff(false, $height, $item ) );

	// top, left positions bug fix
	if (!$detectMobile->isTablet())
	{
		if ($height > 600)
		{
			$resultStyles[]= '.flipbook-viewport .container { margin-left: -'.round($marginLeft/2).'px; margin-top: -'.round($marginTop/2).'px }
	                          .flipbook-viewport .flipbook { left: 0 !important; top: 0 !important; }';
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
	$app = JFactory::getApplication();
	$jinput = $app->input;
	$user = JFactory::getUser();

	$detectMobile = new Mobile_Detect_HTML5FB();

	$rightLeftDeff = rightToLeftDeff($width, $height, $item);
	$cntPages = $item->template->hard_cover ? count($item->pages) + 4 : count($item->pages);

	if ($cntPages % 2 != 0)
	{
		$cntPages += 1;
	}

	$width = $item->template->hard_cover ? 960 : $width;

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
		var publicationID = <?php echo $item->c_id;?>;
		var rawPublicationLink= "<?php echo $item->rawPublicationLink; ?>";
		var rawPublicationTitle = "<?php echo $item->c_title; ?>";
		var sliderThumbs = <?php echo (int)$item->template->slider_thumbs; ?>;
		var previewSrc = "<?php echo COMPONENT_MEDIA_URL; ?>thumbs/preview_<?php echo $item->c_id; ?>.gif?<?php echo time();?>";
		var naviSettings = <?php echo $item->navi_settings;?>;
		var cntPages = <?php echo $cntPages;?>;
		var $mainContainer = $('#mainFlipBookDiv');
		var keyword = "<?php echo $jinput->get('keyword', '', 'STRING');?>";
		var isHard = <?php echo $item->template->hard_cover;?>;
		var isModal = <?php echo ($detectMobile->isTablet() ? 0 : $item->c_popup);?>;
		var isTablet = <?php echo ($detectMobile->isTablet() ? 1 : 0);?>;
		var clickSearch = false;
		var clickGoTo = false;
		var user = <?php echo $user->get('id'); ?>;
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

			$('input[name="publID"]').val(publicationID);

			$('.modalLlink').modal({
				trigger: '.modalLlink',
				olay:'div.html5fb-overlay',
				modals:'div#emailModal',
				animationEffect: 'slidedown',
				animationSpeed: 400,
				moveModalSpeed: 'slow',
				opacity: 0.8,
				openOnLoad: false,
				docClose: true,
				closeByEscape: true,
				moveOnScroll: true,
				resizeWindow: true,
				close:'#closeBtn, .close'
			});

			<?php if ($item->template->display_slider && !$detectMobile->isTablet()): ?>
			$('#slider-bar').css('left', '<?php echo $rightLeftDeff['slider_one_page_left']; ?>px');
			<?php endif; ?>

			$('#fb_bookname').hide();
			$('#search-inp').hide();
			$('#page-bar').hide();

			if ($flipbook.width() == 0 || $flipbook.height() == 0) {
				setTimeout(loadApp, 10);
				return;
			}

			Hash.on('^page\/([0-9]*)$', {
				yep: function(path, parts) {
					var page = parts[1];
					if (typeof(page) !== 'undefined') {
						if ($flipbook.turn('is')) {
							if (naviSettings == 0) {
								page = parseInt(page) + 2;
							}

							$flipbook.turn('page', page);

							if (keyword != '') {
								$('.p' + (page - 1)).children('.flipbook-page').highlight(keyword);
								$('.p' + page).children('.flipbook-page').highlight(keyword);
								$('.p' + (page + 1)).children('.flipbook-page').highlight(keyword);
							}
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
				pages: cntPages,
				when: {
					turning: function(e, page, view) {
						<?php echo $rightLeftDeff['turningSection']; ?>

						fbTurningPage($(this), page, $(this).turn('pages'), <?php echo $rightLeftDeff['slider_one_page_left']; ?>, <?php echo $rightLeftDeff['slider_tho_page_left']; ?>, isTablet);

						if (naviSettings == 0) {
							page = parseInt(page) - 2;

							if (page < 1 || page > (cntPages - 4)) {
								page = 0;
							}
						}

						Hash.go('page/' + page).update();
					},

					turned: function(e, page, view) {
						<?php if ( $item->template->display_slider ) { ?>
						$('#slider').slider('option', 'value', getViewNumber($(this), page));
						<?php } ?>

						<?php if ($item->template->hard_cover) {?>
						updateDepth($(this));
						<?php }?>

						/*Update last open page*/
						if (user) {
							$.ajax({
								type: "POST",
								url: "index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component",
								data: 'pubID=' + publicationID + '&action=updatePage&page=' + page,
								dataType: 'JSON'
							});
						}

						$(this).turn('center');
					},

					start: function(e, pageObj) {
						<?php if ( $item->template->display_slider ) { ?>
						moveBar(true);
						<?php } ?>
					},

					end: function(e, pageObj) {
						<?php if ($item->template->hard_cover) {?>
						updateDepth($(this));
						<?php }?>

						<?php if ( $item->template->display_slider ) { ?>
						setTimeout(function() {
							$('#slider').slider('option', 'value', getViewNumber($(this)));
						}, 1);
						moveBar(false);
						<?php } ?>
					},

					missing: function (e, pages) {
						for (var i = 0; i < pages.length; i++)
							addPage(pages[i], $(this));

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
							if (imgLink.hasOwnProperty(page) && imgLink[page].hasOwnProperty('small') && !isHard) {
								loadSmallPage(page, pageElement, imgLink[page].small);
							}
						}
						else {
							if (imgLink.hasOwnProperty(page) && imgLink[page].hasOwnProperty('large') && !isHard) {
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

							if (isTablet) {
								resizeViewport_Tablet();
							}
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

			<?php if ( $item->template->display_slider ): ?>
			setSliderMaxViews($flipbook);
			<?php endif; ?>

			$flipbook.addClass('animated');

			<?php echo $rightLeftDeff['bindingSection']; ?>

			if (isTablet) {
				$(window).resize(function() {
					if (!clickSearch && !clickGoTo) {
						resizeViewport_Tablet();
					}
				}).bind('orientationchange', function() {
					if (!clickSearch && !clickGoTo) {
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
					clickSearch = false;
					clickGoTo = false;

					if (e.target.name == 'search') {
						e.preventDefault();
						clickSearch = true;
						return false;
					}

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

	//For development purpose
	//print_r($Result);

	//For production
	print_r(preg_replace('/[\s]+/is', ' ', $Result ));
}

//----------------------------------------------------------------------------------------------------
function addFlipDiv($config, $item, $isSearch)
{
	$detectMobile = new Mobile_Detect_HTML5FB();
	?>
	<div id="mainFlipBookDiv">
		<div id="book-wrapper">
			<div class="flipbook-viewport" id="interesting">
				<div class="container" id="zoom-viewport">
					<div class="flipbook html5-book html5-book-transform">
						<div ignore="1" class="fb_topBar <?php echo $detectMobile->isTablet() ? 'tablet-bar' : '';?>">
							<?php if ($item->template->display_title): ?>
								<h2 id="fb_bookname"><?php echo $item->c_title; ?></h2>
							<?php endif; ?>

							<?php if ($isSearch): ?>
								<span id="search-inp">
									<input type="text" name="search" class="search rounded" <?php echo ($detectMobile->isTablet() ? 'style="margin-left: 10px; min-width: 100px;"' : '');?> placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_SEARCH_PLACEHOLDER');?>">
								</span>
							<?php endif;?>

							<?php if ($item->template->display_topicons): ?>
								<div class="tb_social">
									<?php if ($config->social_email_use):?>
										<i class="fa fa-envelope fa-lg modalLlink" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_EMAIL');?>"></i>
									<?php endif;?>

									<?php if (!empty($item->contents_page)):?>
										<i class="fa fa-list fa-lg" onclick="location.href='<?php echo $item->rawPublicationLink.'#page/'.$item->contents_page; ?>'" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_CONTENTS');?>" rel="<?php echo $item->contents_page; ?>"></i>
									<?php endif;?>

									<i class="fa fa-expand fa-lg" id="fullscreen" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_FULLSCREEN'); ?>"></i>

									<i class="fa fa-search-plus fa-lg" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_ZOOM_IN');?>"></i>

									<?php
										if ($item->c_enable_pdf):
											$pdfLink = JURI::root().'index.php?option=' . COMPONENT_OPTION . '&task=convert.getpdf&id=' . $item->c_id . '&filename=' . preg_replace('/[<>:"\/\\\|\?\*]/is', '', $item->c_background_pdf);
										?>
										<i class="fa fa-file-pdf-o fa-lg" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_DOWNLOAD_PDF');?>" onclick="location.href='<?php echo $pdfLink;?>'"></i>
									<?php endif;?>

									<?php if ($config->social_facebook_use):?>
										<i class="fa fa-facebook fa-lg" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_FACEBOOK');?>"></i>
									<?php endif;?>

									<?php if ($config->social_twitter_use):?>
										<i class="fa fa-twitter fa-lg" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_TWITTER');?>"></i>
									<?php endif;?>

									<?php if ($config->social_google_plus_use):?>
										<i class="fa fa-google-plus fa-lg" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_GOOGLEPLUS');?>"></i>
									<?php endif;?>

									<?php if($item->c_popup || $detectMobile->isTablet()):?>
										<i class="fa fa-times fa-lg" title="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_TOOLBAR_CLOSE'); ?>" onclick="<?php echo ($detectMobile->isTablet() ? 'var curTab = window.open(\'\', \'_self\'); curTab.close();' : 'window.close();');?>"></i>
									<?php endif;?>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( $item->template->display_nextprev ) { ?>
							<div ignore="1" class="next-button <?php if ( !$item->template->display_nextprev ) echo 'notvisible'; ?>"></div>
							<div ignore="1" class="previous-button <?php if ( !$item->template->display_nextprev ) echo 'notvisible'; ?>"></div>
						<?php }?>

						<?php
						$additionalClass = '';
						$cntPages = count($item->pages);

						if ($item->template->hard_cover == 1)
						{
							$cntPages += 4;
							$additionalClass = ($detectMobile->isTablet() ? '' : 'class="own-size"');
							echo '<div depth="5" class="hard"><div class="side"></div></div>'."\n";
							echo '<div depth="5" class="hard front-side"><div class="depth"></div></div>'."\n";
						}

						foreach ( $item->pages as $i => $page )
						{
							if ( $page['c_enable_image'] )
							{
								echo '<div ' . $additionalClass . '>'."\n";
								echo '	<div class="gradient"></div>'."\n";
								echo '	<img style="height: 100%; width: 100%;" src="'.COMPONENT_MEDIA_URL. 'images/'. ( $item->c_imgsub ? $item->c_imgsubfolder.'/' : '') . $page['page_image'] . '"/>'."\n";
								echo '</div>'."\n";
							}
							else
							{
								$page['c_text'] = str_replace('src="media/', 'src="' . JUri::root() . 'media/', $page['c_text']);
								$page['c_text'] = str_replace('src="images/', 'src="' . JUri::root() . 'images/', $page['c_text']);
								echo '<div ' . $additionalClass . '><div class="flipbook-page gradient">'.$page['c_text'].'</div></div>'."\n";
							}
						}

						if ($item->template->hard_cover == 1)
						{
							if ($cntPages % 2 != 0)
							{
								echo '<div class="' . ($detectMobile->isTablet() ? '' : 'own-size') .' p' . ($cntPages - 1) . '"></div>' . "\n";
							}

							echo '<div class="hard fixed back-side p' . ($cntPages % 2 == 0 ? $cntPages + 1 : $cntPages) . '"> <div class="depth"></div> </div>'."\n";
							echo '<div class="hard p' . ($cntPages % 2 == 0 ? $cntPages + 2 : $cntPages + 1) . '"></div>'."\n";
						}
						?>

						<?php if ($detectMobile->isTablet()):?>
						<div ignore="1" class="tablet-bottomBar">
							<?php endif;?>

							<?php if ($item->template->display_pagebox): ?>
								<div ignore="1" id="page-bar">
									<label><?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_GOTO_PAGE_TITLE'); ?></label>
									<?php if ($detectMobile->isTablet()):?>
										<input type="number" step="1" id="goto_page_input" value="" autocomplete="" placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_GOTO_PAGE'); ?>" />
									<?php else: ?>
										<input type="text" id="goto_page_input" value="" autocomplete="" placeholder="<?php echo JText::_('COM_HTML5FLIPPINGBOOK_FE_GOTO_PAGE'); ?>" />
									<?php endif; ?>
									<span id="goto_page_input_button" onclick="location.href='<?php echo $item->rawPublicationLink; ?>#page/'+document.getElementById('goto_page_input').value;"></span>
								</div>
							<?php endif; ?>

							<?php if ($item->template->display_slider): ?>
								<div ignore="1" id="slider-bar" class="fb_slider">
									<div id="slider"></div>
								</div>
							<?php endif; ?>

							<?php if ($detectMobile->isTablet()):?>
						</div>
					<?php endif;?>
					</div>
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

	$pageLink = JRoute::_('index.php?option='.COMPONENT_OPTION.'&view=publication&id='.$this->item->c_id, FALSE, $uri->isSSL());

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
	$doc->addCustomTag('<meta property="og:image" content="'.str_replace(' ','%20',$opengraphImage).'" />');
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

// add viewpoint tag for mobile
$doc->addCustomTag('<meta name="viewport" content="width = 1050, user-scalable = no" />');
$doc->addScript(COMPONENT_JS_URL . 'jquery-1.10.2.min.js');
$doc->addScript(COMPONENT_JS_URL . 'jquery.mousewheel.min.js');
$doc->addScript(COMPONENT_JS_URL . 'jquery-zoom.min.js');
$doc->addScript(COMPONENT_JS_URL . 'jquery.modal.min.js');

//Modified file jquery ui, issue: Cannot call method 'addClass' of undefined - connected with closestHandler in slider widget (https://gist.github.com/tlack/3745667)
$doc->addScript(COMPONENT_JS_URL . 'jquery-ui-1.10.4.custom.min.js');
if ($this->isSearch)
{
	$doc->addScript(COMPONENT_JS_URL . 'jquery.liveSearch.min.js');
	$doc->addScript(COMPONENT_JS_URL . 'jquery.highlight.min.js');
}
$doc->addScript(COMPONENT_JS_URL . 'screenfull.min.js');
$doc->addScript(COMPONENT_JS_URL . 'html5fb.scripts.min.js');
$doc->addStyleSheet(JUri::root() . 'index.php?option='.COMPONENT_OPTION.'&task=templatecss&template_id='.$this->item->c_template_id);
$doc->addStyleSheet(COMPONENT_CSS_URL . 'modal.css');

addAdditionStylesDeclaration( $FlipWidth, $FlipHeight, $this->item);

JText::script('COM_HTML5FLIPPINGBOOK_FE_FULLSCREEN_ALERT');

if ($this->tmplIsComponent)
{
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
	<?php
	$hdata = $doc->getHeadData();
	foreach ($hdata['scripts'] as $url => $params)
		if ( strpos($url, 'html5fb') === false && strpos($url, 'jquery') == false && strpos($url, 'screenfull') === false)
			unset( $hdata['scripts'][$url]);

	if ( !empty($hdata['custom']) )
		foreach ($hdata['custom'] as $k => $value )
			if ( strpos($value, '<script') !== false )
				unset($hdata['custom'][$k]);

	$doc->setHeadData($hdata);

	echo $doc->loadRenderer("head")->fetchHead($doc);
	?>
</head>

<body style="overflow: hidden; margin: 0;">
<div class="load-overlay" style="display: block;">
	<img class="load-image" src="<?php echo COMPONENT_IMAGES_URL; ?>progress.gif" alt="">
</div>
<?php addFlipDiv($this->config, $this->item, $this->isSearch); ?>
<?php addFlipRunningScripts($FlipWidth, $FlipHeight, $this->item, TRUE); ?>
<?php
if ($this->config->social_email_use)
{
	echo $this->layout->render(NULL);
}
?>
</body>
</html>
	<?php
	jexit();
}
else
{
	addFlipDiv($this->config, $this->item, $this->isSearch);
	addFlipRunningScripts( $FlipWidth, $FlipHeight, $this->item, FALSE);
}