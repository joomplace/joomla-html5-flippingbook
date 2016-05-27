/* Steve jobs' book */

function updateDepth(book, newPage) {

	var page = newPage,
		pages = book.turn('pages'),
		depthC = 0,
		depthM = (pages>48)?16:(pages/3),
		selectorLeft = book.find('.depth-left'),
		selectorRight = book.find('.depth-right');
		
	if(!selectorLeft.length){
		book.append('<div class="depth depth-left"></div>');
	}
	if(!selectorRight.length){
		book.prepend('<div class="depth depth-right"></div>');
	}
	
	if(pages>5)
		depthC = Math.min((page-3)/(pages-5));
	
	if(0>depthC) depthC = 0;
	else if(depthC>1) depthC = 1;
	selectorRight.css({width: depthC*depthM});
	selectorLeft.css({width: depthM*(1-depthC)});

}
			
			

function addPage(page, book, adj) {

	var id, pages = book.turn('pages'),
		size = book.turn('size');
		
	if (!book.turn('hasPage', page)) {

		var element = jQuery('<div />',
			{'class': 'page p'+page,
				css: {width: (size.width/2), height: size.height}
			}).
			html('<div class="paddifier"><div class="loader"><i class="fa fa-spinner fa-spin fa-pulse"></i></div></div>');
			
		if (book.turn('addPage', element, page)) {
			loadPage(page,adj);
		}

	}
}

function numberOfViews(book) {

	return book.turn('pages') / 2 + 1;

}

function getViewNumber(book, page) {

	return parseInt((page || book.turn('page'))/2 + 1, 10);

}

function zoomThis(pic) {
// issue: Cannot read property 'left' of null
	var	position, translate,
		tmpContainer = jQuery('<div />', {'class': 'zoom-pic'}),
		transitionEnd = jQuery.cssTransitionEnd(),
		tmpPic = jQuery('<img />'),
		zCenterX = jQuery('#book-zoom').width()/2,
		zCenterY = jQuery('#book-zoom').height()/2,
		bookPos = jQuery('#book-zoom').offset(),
		picPos = {
			left: pic.offset().left - bookPos.left,
			top: pic.offset().top - bookPos.top
		},
		completeTransition = function() {
			jQuery('#book-zoom').unbind(transitionEnd);

			if (flipbook.data().zoomIn) {
				tmpContainer.appendTo(jQuery('body'));

				jQuery('body').css({'overflow': 'hidden'});
				
				tmpPic.css({
					margin: position.top + 'px ' + position.left+'px'
				}).
				appendTo(tmpContainer).
				fadeOut(0).
				fadeIn(500);
			}
		};

		flipbook.data().zoomIn = true;

		flipbook.turn('disable', true);

		jQuery(window).resize(zoomOut);
		
		tmpContainer.click(zoomOut);

		tmpPic.load(function() {
			var realWidth = jQuery(this)[0].width,
				realHeight = jQuery(this)[0].height,
				zoomFactor = realWidth/pic.width(),
				picPosition = {
					top:  (picPos.top - zCenterY)*zoomFactor + zCenterY + bookPos.top,
					left: (picPos.left - zCenterX)*zoomFactor + zCenterX + bookPos.left
				};


			position = {
				top: (jQuery(window).height()-realHeight)/2,
				left: (jQuery(window).width()-realWidth)/2
			};

			translate = {
				top: position.top-picPosition.top,
				left: position.left-picPosition.left
			};

			jQuery('.samples .bar').css({visibility: 'hidden'});
			jQuery('#slider-bar').hide();
			
		
			jQuery('#book-zoom').transform(
				'translate('+translate.left+'px, '+translate.top+'px)' +
				'scale('+zoomFactor+', '+zoomFactor+')');

			if (transitionEnd)
				jQuery('#book-zoom').bind(transitionEnd, completeTransition);
			else
				setTimeout(completeTransition, 1000);

		});

		tmpPic.attr('src', pic.attr('src'));

}

function zoomOut() {

	var transitionEnd = jQuery.cssTransitionEnd(),
		completeTransition = function(e) {
			jQuery('#book-zoom').unbind(transitionEnd);
			flipbook.turn('disable', false);
			jQuery('body').css({'overflow': 'auto'});
			moveBar(false);
		};

	flipbook.data().zoomIn = false;

	jQuery(window).unbind('resize', zoomOut);

	moveBar(true);

	jQuery('.zoom-pic').remove();
	jQuery('#book-zoom').transform('scale(1, 1)');
	jQuery('.samples .bar').css({visibility: 'visible'});
	jQuery('#slider-bar').show();

	if (transitionEnd)
		jQuery('#book-zoom').bind(transitionEnd, completeTransition);
	else
		setTimeout(completeTransition, 1000);
}


function moveBar(yes) {
	if (Modernizr && Modernizr.csstransforms) {
		jQuery('#slider .ui-slider-handle').css({zIndex: yes ? -1 : 10000});
	}
}

function setPreview(view, file, full) {

	var previewWidth = 115,
		previewHeight = 73,
		previewSrc = file,
		preview = jQuery(_thumbPreview.children(':first')),
		numPages = (view==1 || view==jQuery('#slider').slider('option', 'max')) ? 1 : 2,
		width = (numPages==1) ? previewWidth/2 : previewWidth;

	_thumbPreview.
		addClass('no-transition').
		css({width: width + 15,
			height: previewHeight + 15,
			top: -previewHeight - 30,
			left: (jQuery(jQuery('#slider').children(':first')).width() - width - 15)/2
		});

	preview.css({
		width: width,
		height: previewHeight
	});

	if (preview.css('background-image')==='' ||
		preview.css('background-image')=='none') {

		preview.css({backgroundImage: 'url(' + previewSrc + ')'});

		setTimeout(function(){
			_thumbPreview.removeClass('no-transition');
		}, 0);

	}
	if(!full){
		preview.css({backgroundPosition:
			'0px -'+((view-1)*previewHeight)+'px'
		});
	}
}

function isChrome() {

	// Chrome's unsolved bug
	// http://code.google.com/p/chromium/issues/detail?id=128488

	return navigator.userAgent.indexOf('Chrome')!=-1;

}