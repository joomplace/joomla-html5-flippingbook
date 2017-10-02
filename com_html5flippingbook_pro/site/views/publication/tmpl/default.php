<?php 
defined('_JEXEC') or die('Restricted access');
/*
* HTML5FlippingBook Component
* @package HTML5FlippingBook
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
$document = JFactory::getDocument();
JHtml::_('jquery.framework', true, null, true);
JHtml::_('jquery.ui');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/extras/modernizr.2.5.3.min.js');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/extras/jquery.mousewheel.min.js');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/lib/hash.js');
$document->addScript(JUri::root(true).'/components/com_html5flippingbook/assets/lib/turn.min.js');
$document->addScriptDeclaration("
	var flipbook = jQuery('.flipbook');
");

?>
<style>
html, body {
    margin: 0;
    height: 100%;
}

body.flip-hide-overflow {
    overflow: hidden;
}

.flipbook-viewport{
    max-width: 1200px;
}
	
/* helpers */

.flipbook-viewport {
    display: table;
    width: 100%;
    height: 100%;
}

.container {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
}

.rel {
    position: relative;
}

/* book */

.flipbook {
    margin: 0 auto !important;
    width: 90%;
    height: 90%;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.flipbook .even .double,
.flipbook .even .page {
    background-size: 200%;
    background-position: 0%;
}
.flipbook .double,
.flipbook .page {
    height: 100%;
    background-size: auto 100%;
    background-repeat: no-repeat;
    background-position: 100%;
}

.flipbook .double img,
.flipbook .page img {
    max-width: 100%;
    height: 100%;
}

/* hard */
.hard.cover-front{
	background-image: url('http://www.joomplace.com/images/covers2/bigbox/html5flipbook_big.jpg');
}
.hard.cover-back{
	background-image: url('http://www.joomplace.com/images/covers2/bigbox/html5flipbook_big.jpg');
}
.hard.front-side{
	background-image: url('<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/1.jpg');
}
.hard.back-side{
	background-image: url('<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/7.jpg');
}
.tbicon {
    background-image: url("http://demo30.joomplace.com/components/com_html5flippingbook/assets/images/new-sprites.png");
    display: inline-block;
    height: 22px;
    width: 22px;
    cursor: pointer;
    margin: 0 20px;
}
.zoom-out, .zoom-out-hover {
    background-position: -220px 0;
    width: 44px;
    height: 44px;
    position: fixed;
    top: 30px;
    right: 48%;
    left: 48%;
    z-index: 1000;
}
.flipbook-viewport > .rel{
	padding: 20px 0px;
}


</style>

<div class="flip-hide-overflow">
	<div class="flipbook-viewport">
		<div class="rel">
				<div id="flipbook" class="flipbook">
					<div depth="5" class="hard cover-front"> <div class="side"></div> </div>
					<div depth="5" class="hard front-side"> <div class="depth"></div> </div>
					<div class="page"><img src="<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/1.jpg" /></div>
					<div class="page"><img src="<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/2.jpg" /></div>
					<div class="page"><img src="<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/3.jpg" /></div>
					<div class="page"><img src="<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/4.jpg" /></div>
					<div class="page"><img src="<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/5.jpg" /></div>
					<div class="page"><img src="<?php echo JUri::root(true).'/components/com_html5flippingbook/'; ?>pages/6.jpg" /></div>
					<div class="hard back-side fixed p9"> <div class="depth"></div> </div>
					<div class="hard cover-back p10"></div>
				</div>
		</div>
	</div>
	<i class="tbicon zoom-out" style="display: none;"></i>
</div>


<script type="text/javascript">
(function ($) {
	function zoomIn(book){
		$('.tbicon.zoom-out').show();
		book.turn('zoom',2);
		//book.turn('disable', true);
		
		/* add mouse move scroll */
		// http://stackoverflow.com/questions/6518600/scroll-window-when-mouse-moves
		// http://stackoverflow.com/questions/27924066/scroll-div-content-on-mouse-move
		
	}
	function zoomOut(book){
		$('.tbicon.zoom-out').hide();
		book.turn('zoom',1);
		//book.turn('disable', false);
	}
	
	function zoomHandle(book) {
		if(book.turn('zoom')!=1){
			zoomOut(book);
		}else{
			zoomIn(book);
		}

	}

	'use strict';
	var module = {
		ratio: 2,
		init: function (id) {
			var me = this;
			// if older browser then don't run javascript
			if (document.addEventListener) {
				this.el = document.getElementById(id);
				this.resize();
				this.plugins();
				// on window resize, update the plugin size
				window.addEventListener('resize', function (e) {
					var size = me.resize();
					zoomOut($(me.el));
					$(me.el).turn('size',size.width,size.height);
				});
			}
		},
		resize: function () {
			// reset the width and height to the css defaults
			this.el.style.width = '';
			this.el.style.height = '';
			var width = this.el.clientWidth,
				height = Math.round(width / this.ratio),
				padded = Math.round(document.body.clientHeight * 0.9);
			// if the height is too big for the window, constrain it
			if (height > padded) {
				height = padded;
				width = Math.round(height * this.ratio);
			}
			// set the width and height matching the aspect ratio
			this.el.style.width = width + 'px';
			this.el.style.height = height + 'px';
			return {
				width: width,
				height: height
			};
		},
		plugins: function () {
			var flipbook = $(this.el)
			
			// URIs
			Hash.on('^page\/([0-9]*)$', {
				yep: function(path, parts) {
					var page = parts[1];
					if (page!==undefined) {
						if (flipbook.turn('is'))
							flipbook.turn('page', page);
					}
				},
				nop: function(path) {
					if (flipbook.turn('is'))
						flipbook.turn('page', 1);
				}
			});
			// Arrows
			$(document).keydown(function(e){
				var previous = 37, next = 39;
				switch (e.keyCode) {
					case previous:
						flipbook.turn('previous');
					break;
					case next:
						flipbook.turn('next');
					break;
				}
			});
			
			// Flipbook
			/* choose other events!!! */
			flipbook.bind(($.isTouch) ? 'doubletap' : 'dblclick', function(e){zoomHandle(flipbook);});
			
			$('.tbicon.zoom-out').on('click',function(e){
					zoomOut(flipbook);
					return false;
				});
			
			flipbook.find('.double').scissor();
			
			flipbook.turn({
				elevation: 50,
				acceleration: !isChrome(),
				autoCenter: true,
				gradients: true,
				duration: 1000,
				//pages: 16,
				when: {
					turning: function(e, page, view) {
						
						var book = $(this),
							currentPage = book.turn('page'),
							pages = book.turn('pages');

						if (currentPage>3 && currentPage<pages-3) {
						
							if (page==1) {
								book.turn('page', 2).turn('stop').turn('page', page);
								e.preventDefault();
								return;
							} else if (page==pages) {
								book.turn('page', pages-1).turn('stop').turn('page', page);
								e.preventDefault();
								return;
							}
						} else if (page>3 && page<pages-3) {
							if (currentPage==1) {
								book.turn('page', 2).turn('stop').turn('page', page);
								e.preventDefault();
								return;
							} else if (currentPage==pages) {
								book.turn('page', pages-1).turn('stop').turn('page', page);
								e.preventDefault();
								return;
							}
						}

						updateDepth(book, page);
						
						if (page>=2)
							flipbook.find('.p2').addClass('fixed');
						else
							flipbook.find('.p2').removeClass('fixed');

						if (page<book.turn('pages'))
							flipbook.find('.back-side').addClass('fixed');
						else
							flipbook.find('.back-side').removeClass('fixed');

						Hash.go('page/'+page).update();
							
					},

					turned: function(e, page, view) {

						var book = $(this);

						if (page==2 || page==3) {
							book.turn('peel', 'br');
						}

						updateDepth(book);
						
						//$('#slider').slider('value', getViewNumber(book, page));

						book.turn('center');

					},
					
					zooming: function(e, newFactor, current) {

						if(newFactor!=1){
							$('.flip-hide-overflow').css('overflow','scroll');
						}else{
							$('.flip-hide-overflow').css('overflow','');
						}

					},

					start: function(e, pageObj) {
				
						moveBar(true);

					},

					end: function(e, pageObj) {
					
						var book = $(this);

						updateDepth(book);

						setTimeout(function() {
							
							//$('#slider').slider('value', getViewNumber(book));

						}, 1);
						
						moveBar(false);

					},

					last: function(e) {
						
						var book = $(this);
						zoomOut(book);

					},

					first: function(e) {
						
						var book = $(this);
						zoomOut(book);

					},

					missing: function (e, pages) {

						for (var i = 0; i < pages.length; i++) {
							addPage(pages[i], $(this));
						}

					}
				}
			});

			flipbook.addClass('animated');
			// hide the body overflow
			document.body.className = 'flip-hide-overflow';
		}
	};
	
	function loadApp() {
		// Check if the CSS was already loaded	
		if (flipbook.width()==0 || flipbook.height()==0) {
			setTimeout(loadApp, 10);
			return;
		}
		module.init('flipbook');
	}
	
	// Load the HTML4 version if there's not CSS transform
	yepnope({
		test : Modernizr.csstransforms,
		yep: ['<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>lib/turn.min.js'],
		nope: ['<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>lib/turn.html4.min.js', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>css/jquery.ui.html4.css'],
		both: ['<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>js/cust.js', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>lib/scissor.min.js', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>css/jquery.ui.css', '<?php echo JUri::root(true).'/components/com_html5flippingbook/assets/'; ?>css/double-page.css'],
		complete: loadApp
	});
}(jQuery));

</script>