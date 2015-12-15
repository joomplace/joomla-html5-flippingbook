/* Modernizr 2.5.3 (Custom Build) | MIT & BSD */
;window.Modernizr=function(a,b,c){function z(a){j.cssText=a}function A(a,b){return z(m.join(a+";")+(b||""))}function B(a,b){return typeof a===b}function C(a,b){return!!~(""+a).indexOf(b)}function D(a,b){for(var d in a)if(j[a[d]]!==c)return b=="pfx"?a[d]:!0;return!1}function E(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:B(f,"function")?f.bind(d||b):f}return!1}function F(a,b,c){var d=a.charAt(0).toUpperCase()+a.substr(1),e=(a+" "+o.join(d+" ")+d).split(" ");return B(b,"string")||B(b,"undefined")?D(e,b):(e=(a+" "+p.join(d+" ")+d).split(" "),E(e,b,c))}var d="2.5.3",e={},f=!0,g=b.documentElement,h="modernizr",i=b.createElement(h),j=i.style,k,l={}.toString,m=" -webkit- -moz- -o- -ms- ".split(" "),n="Webkit Moz O ms",o=n.split(" "),p=n.toLowerCase().split(" "),q={},r={},s={},t=[],u=t.slice,v,w=function(a,c,d,e){var f,i,j,k=b.createElement("div"),l=b.body,m=l?l:b.createElement("body");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:h+(d+1),k.appendChild(j);return f=["&#173;","<style>",a,"</style>"].join(""),k.id=h,(l?k:m).innerHTML+=f,m.appendChild(k),l||(m.style.background="",g.appendChild(m)),i=c(k,a),l?k.parentNode.removeChild(k):m.parentNode.removeChild(m),!!i},x={}.hasOwnProperty,y;!B(x,"undefined")&&!B(x.call,"undefined")?y=function(a,b){return x.call(a,b)}:y=function(a,b){return b in a&&B(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=u.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(u.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(u.call(arguments)))};return e});var G=function(a,c){var d=a.join(""),f=c.length;w(d,function(a,c){var d=b.styleSheets[b.styleSheets.length-1],g=d?d.cssRules&&d.cssRules[0]?d.cssRules[0].cssText:d.cssText||"":"",h=a.childNodes,i={};while(f--)i[h[f].id]=h[f];e.csstransforms3d=(i.csstransforms3d&&i.csstransforms3d.offsetLeft)===9&&i.csstransforms3d.offsetHeight===3},f,c)}([,["@media (",m.join("transform-3d),("),h,")","{#csstransforms3d{left:9px;position:absolute;height:3px;}}"].join("")],[,"csstransforms3d"]);q.csstransforms=function(){return!!F("transform")},q.csstransforms3d=function(){var a=!!F("perspective");return a&&"webkitPerspective"in g.style&&(a=e.csstransforms3d),a};for(var H in q)y(q,H)&&(v=H.toLowerCase(),e[v]=q[H](),t.push((e[v]?"":"no-")+v));return z(""),i=k=null,function(a,b){function g(a,b){var c=a.createElement("p"),d=a.getElementsByTagName("head")[0]||a.documentElement;return c.innerHTML="x<style>"+b+"</style>",d.insertBefore(c.lastChild,d.firstChild)}function h(){var a=k.elements;return typeof a=="string"?a.split(" "):a}function i(a){var b={},c=a.createElement,e=a.createDocumentFragment,f=e();a.createElement=function(a){var e=(b[a]||(b[a]=c(a))).cloneNode();return k.shivMethods&&e.canHaveChildren&&!d.test(a)?f.appendChild(e):e},a.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+h().join().replace(/\w+/g,function(a){return b[a]=c(a),f.createElement(a),'c("'+a+'")'})+");return n}")(k,f)}function j(a){var b;return a.documentShived?a:(k.shivCSS&&!e&&(b=!!g(a,"article,aside,details,figcaption,figure,footer,header,hgroup,nav,section{display:block}audio{display:none}canvas,video{display:inline-block;*display:inline;*zoom:1}[hidden]{display:none}audio[controls]{display:inline-block;*display:inline;*zoom:1}mark{background:#FF0;color:#000}")),f||(b=!i(a)),b&&(a.documentShived=b),a)}var c=a.html5||{},d=/^<|^(?:button|form|map|select|textarea)$/i,e,f;(function(){var a=b.createElement("a");a.innerHTML="<xyz></xyz>",e="hidden"in a,f=a.childNodes.length==1||function(){try{b.createElement("a")}catch(a){return!0}var c=b.createDocumentFragment();return typeof c.cloneNode=="undefined"||typeof c.createDocumentFragment=="undefined"||typeof c.createElement=="undefined"}()})();var k={elements:c.elements||"abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video",shivCSS:c.shivCSS!==!1,shivMethods:c.shivMethods!==!1,type:"default",shivDocument:j};a.html5=k,j(b)}(this,b),e._version=d,e._prefixes=m,e._domPrefixes=p,e._cssomPrefixes=o,e.testProp=function(a){return D([a])},e.testAllProps=F,e.testStyles=w,g.className=g.className.replace(/(^|\s)no-js(\s|$)/,"$1$2")+(f?" js "+t.join(" "):""),e}(this,this.document),function(a,b,c){function d(a){return o.call(a)=="[object Function]"}function e(a){return typeof a=="string"}function f(){}function g(a){return!a||a=="loaded"||a=="complete"||a=="uninitialized"}function h(){var a=p.shift();q=1,a?a.t?m(function(){(a.t=="c"?B.injectCss:B.injectJs)(a.s,0,a.a,a.x,a.e,1)},0):(a(),h()):q=0}function i(a,c,d,e,f,i,j){function k(b){if(!o&&g(l.readyState)&&(u.r=o=1,!q&&h(),l.onload=l.onreadystatechange=null,b)){a!="img"&&m(function(){t.removeChild(l)},50);for(var d in y[c])y[c].hasOwnProperty(d)&&y[c][d].onload()}}var j=j||B.errorTimeout,l={},o=0,r=0,u={t:d,s:c,e:f,a:i,x:j};y[c]===1&&(r=1,y[c]=[],l=b.createElement(a)),a=="object"?l.data=c:(l.src=c,l.type=a),l.width=l.height="0",l.onerror=l.onload=l.onreadystatechange=function(){k.call(this,r)},p.splice(e,0,u),a!="img"&&(r||y[c]===2?(t.insertBefore(l,s?null:n),m(k,j)):y[c].push(l))}function j(a,b,c,d,f){return q=0,b=b||"j",e(a)?i(b=="c"?v:u,a,b,this.i++,c,d,f):(p.splice(this.i++,0,a),p.length==1&&h()),this}function k(){var a=B;return a.loader={load:j,i:0},a}var l=b.documentElement,m=a.setTimeout,n=b.getElementsByTagName("script")[0],o={}.toString,p=[],q=0,r="MozAppearance"in l.style,s=r&&!!b.createRange().compareNode,t=s?l:n.parentNode,l=a.opera&&o.call(a.opera)=="[object Opera]",l=!!b.attachEvent&&!l,u=r?"object":l?"script":"img",v=l?"script":u,w=Array.isArray||function(a){return o.call(a)=="[object Array]"},x=[],y={},z={timeout:function(a,b){return b.length&&(a.timeout=b[0]),a}},A,B;B=function(a){function b(a){var a=a.split("!"),b=x.length,c=a.pop(),d=a.length,c={url:c,origUrl:c,prefixes:a},e,f,g;for(f=0;f<d;f++)g=a[f].split("="),(e=z[g.shift()])&&(c=e(c,g));for(f=0;f<b;f++)c=x[f](c);return c}function g(a,e,f,g,i){var j=b(a),l=j.autoCallback;j.url.split(".").pop().split("?").shift(),j.bypass||(e&&(e=d(e)?e:e[a]||e[g]||e[a.split("/").pop().split("?")[0]]||h),j.instead?j.instead(a,e,f,g,i):(y[j.url]?j.noexec=!0:y[j.url]=1,f.load(j.url,j.forceCSS||!j.forceJS&&"css"==j.url.split(".").pop().split("?").shift()?"c":c,j.noexec,j.attrs,j.timeout),(d(e)||d(l))&&f.load(function(){k(),e&&e(j.origUrl,i,g),l&&l(j.origUrl,i,g),y[j.url]=2})))}function i(a,b){function c(a,c){if(a){if(e(a))c||(j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}),g(a,j,b,0,h);else if(Object(a)===a)for(n in m=function(){var b=0,c;for(c in a)a.hasOwnProperty(c)&&b++;return b}(),a)a.hasOwnProperty(n)&&(!c&&!--m&&(d(j)?j=function(){var a=[].slice.call(arguments);k.apply(this,a),l()}:j[n]=function(a){return function(){var b=[].slice.call(arguments);a&&a.apply(this,b),l()}}(k[n])),g(a[n],j,b,n,h))}else!c&&l()}var h=!!a.test,i=a.load||a.both,j=a.callback||f,k=j,l=a.complete||f,m,n;c(h?a.yep:a.nope,!!i),i&&c(i)}var j,l,m=this.yepnope.loader;if(e(a))g(a,0,m,0);else if(w(a))for(j=0;j<a.length;j++)l=a[j],e(l)?g(l,0,m,0):w(l)?B(l):Object(l)===l&&i(l,m);else Object(a)===a&&i(a,m)},B.addPrefix=function(a,b){z[a]=b},B.addFilter=function(a){x.push(a)},B.errorTimeout=1e4,b.readyState==null&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",A=function(){b.removeEventListener("DOMContentLoaded",A,0),b.readyState="complete"},0)),a.yepnope=k(),a.yepnope.executeStack=h,a.yepnope.injectJs=function(a,c,d,e,i,j){var k=b.createElement("script"),l,o,e=e||B.errorTimeout;k.src=a;for(o in d)k.setAttribute(o,d[o]);c=j?h:c||f,k.onreadystatechange=k.onload=function(){!l&&g(k.readyState)&&(l=1,c(),k.onload=k.onreadystatechange=null)},m(function(){l||(l=1,c(1))},e),i?k.onload():n.parentNode.insertBefore(k,n)},a.yepnope.injectCss=function(a,c,d,e,g,i){var e=b.createElement("link"),j,c=i?h:c||f;e.href=a,e.rel="stylesheet",e.type="text/css";for(j in d)e.setAttribute(j,d[j]);g||(n.parentNode.insertBefore(e,n),m(c,0))}}(this,document),Modernizr.load=function(){yepnope.apply(window,[].slice.call(arguments,0))};
/* HASH */
(function(){var b={},h=0,f=!1,k=null,g=null,l=function(){k||(k=setInterval(function(){0<h&&g!=window.location.href&&(g=window.location.href,window.Hash.check())},100))};window.Hash=function(a){return Object.freeze?Object.freeze(a):a}({pushState:function(a){window.history&&window.history.pushState&&(f=a);return this},fragment:function(){var a=window.location.href.split("#");return f?window.location.pathname+(a[1]?"#"+a[1]:""):a[1]||""},get:function(a,c){var b,d=[];for(b in c)Object.prototype.hasOwnProperty(b)&& d.push(encodeURIComponent(b)+"="+encodeURIComponent(c[b]));0<d.length&&(d="?"+d.join("&"));return f?a+d:window.location.href.split("#")[0]+"#"+a+d},go:function(a,b){if(this.fragment()!=a){var e=this.get(a,b);f?window.history.pushState(null,document.title,e):window.location.href=e}return this},update:function(){g=window.location.href;return this},on:function(a,c,e){b[a]||(b[a]={title:e,listeners:[]});b[a].listeners.push(c);h++;l();return this},check:function(){var a,c,e,d=this.fragment();for(c in b)if(Object.prototype.hasOwnProperty.call(b, c))if(b[c].regexp=b[c].regexp||RegExp(c),e=b[c].regexp.exec(d))for(b[c].title&&(document.title=b[c].title),a=0;a<b[c].listeners.length;a++)b[c].listeners[a].yep&&b[c].listeners[a].yep(d,e);else for(a=0;a<b[c].listeners.length;a++)b[c].listeners[a].nop&&b[c].listeners[a].nop(d);return this}})})();

function clickElement(element, func) {
    if ($.isTouch) {
        element.bind($.mouseEvents.up, func);
    } else {
        $(element).on('click', func);
    }
}

function navigation(where) {
    switch (where) {
        case 'table-contents' :
            $('.flipbook').turn('page', $('.table-contents').attr('rel'));
            break;
        case 'fa fa-facebook fa-lg' :
            window.open('https://www.facebook.com/sharer.php?' +
                'u=' + encodeURIComponent(rawPublicationLink) +
                '&t=' + encodeURIComponent(rawPublicationTitle));
            break;
        case 'fa fa-twitter fa-lg' :
            window.open('https://twitter.com/intent/tweet?' +
                'original_referer=' + encodeURIComponent(rawPublicationLink) +
                '&url=' + encodeURIComponent(rawPublicationLink) +
                '&text=' + encodeURIComponent(rawPublicationTitle));
            break;
        case 'fa fa-pinterest fa-lg' :
            window.open('http://pinterest.com/pin/create/button/?url=' +
                'url=' + encodeURIComponent(rawPublicationLink) +
                '&media=' + encodeURIComponent(rawPublicationTitle));
            break;
        case 'fa fa-google-plus fa-lg' :
            window.open('https://plusone.google.com/_/+1/confirm?' +
                'url=' + encodeURIComponent(rawPublicationLink));
            break;
    }
}

function checkCloseBtn() {
    if (window.top !== window.self) {
        $('.fa-times').hide();
    }
}

function turnPage(pageN) {
    $('#jquery-live-search').slideUp(400); //hide search results

    $('.flipbook').turn('page', pageN); //turn page

    if (isTablet) {
        clickSearch = false;
        clickGoTo = false;
    }

    $('.search-result').removeClass('current');
    $('#page-' + pageN).addClass('current'); //add class to selected page

    var searchTXT = '';
    if (keyword != '') {
        searchTXT = keyword;
    }
    else {
        searchTXT = $('#search-inp input[name="search"]').val();
    }
    $('.p' + (pageN - 1)).children('.flipbook-page').highlight(searchTXT);
    $('.p' + pageN).children('.flipbook-page').highlight(searchTXT);
    $('.p' + (pageN + 1)).children('.flipbook-page').highlight(searchTXT);
}

function Countdown(options) {
    var timer,
        instance = this,
        seconds = options.seconds || 10,
        updateStatus = options.onUpdateStatus || function () {},
        counterEnd = options.onCounterEnd || function () {};

    function decrementCounter() {
        updateStatus(seconds);
        if (seconds === 0) {
            counterEnd();
            instance.stop();
        }
        seconds--;
    }

    this.start = function () {
        clearInterval(timer);
        timer = 0;
        seconds = options.seconds;
        timer = setInterval(decrementCounter, 1000);
    };

    this.stop = function () {
        clearInterval(timer);
    };
}

(function($){
    $.getQuery = function( query ) {
        query = query.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var expr = "[\\?&]"+query+"=([^&#]*)";
        var regex = new RegExp( expr );
        var results = regex.exec( window.location.href );
        if( results !== null ) {
            return results[1];
            return decodeURIComponent(results[1].replace(/\+/g, " "));
        } else {
            return false;
        }
    };
})(jQuery);

$(window).load(function () {
    $(".load-overlay").fadeOut(1000);
});

if (screenfull.enabled) {
    document.addEventListener(screenfull.raw.fullscreenchange, function () {
        if (screenfull.isFullscreen && isTablet) {
            resizeViewport_Tablet();
        }
    });
}

function resizeViewport() {
    var width = $(window).width(),
        height = $(window).height(),
        options = $('.flipbook').turn('options');

    $('.flipbook-viewport').css({
        width: width,
        height: height
    });
}

$(document).ready(function () {
    screenfull.request( document.getElementById('mainFlipBookDiv') );

    $('.fa-search-plus').on('click', function(event) {
        var currPage = $('.flipbook').turn('page');
        var cntPages = $('.flipbook').turn('pages');
        if (typeof imgLink=='object' && ((imgLink.hasOwnProperty(currPage) && imgLink[currPage].hasOwnProperty('large') && imgLink[currPage].hasOwnProperty('small')) &&
            (currPage == cntPages || (imgLink.hasOwnProperty(currPage+1) && imgLink[currPage+1].hasOwnProperty('large') && imgLink[currPage+1].hasOwnProperty('small')))
            )) {
            $('#mainFlipBookDiv').zoom('zoomIn');
        }
        else {
            if ($('.flipbook').data().zoomIn) {
                zoomOutText();
            }
            else {
                zoomInText(event, true);
            }
        }
    });

    $('.fa-expand').click(function() {
        if (isModal == 3 && $.getQuery('fullscreen') != 1) {
            var newWin = window.open(window.location.href + '&fullscreen=1');
            window.opener = null;
            newWin.focus();
            return;
        }
        if (screenfull) {
            screenfull.request( document.getElementById('mainFlipBookDiv') );
        }
    });

    if ($.getQuery('fullscreen') == 1) {
        alert('If you want to enable fullscreen mode, please press on "Fullscreen" button');
    }

    if (screenfull.enabled) {
        document.addEventListener(screenfull.raw.fullscreenchange, function () {
            if (screenfull.isFullscreen) {
                resizeViewport();
            }
            else if (isModal == 1) {
                location.reload();
            }
        });
    }

    if (user) {
        var examTimer = new Countdown({
            seconds: 90,  // number of seconds to count down
            onCounterEnd: function(){
                $.ajax({
                    type: "POST",
                    url: "index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component",
                    data: 'pubID=' + publicationID + '&action=updateSpendTime&sec=90',
                    dataType: 'JSON',
                    success: function() {
                        examTimer.start();
                    }
                });
            } // final action
        });
        examTimer.start();

        //Update date of the last open publication for current user
        $.ajax({
            type: "POST",
            url: "index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component",
            data: 'pubID=' + publicationID + '&action=lastopen',
            dataType: 'JSON'
        });
    }

    checkCloseBtn();

    $('#goto_page_input').on('keyup', function (e) {
        if (e.which == 13) {
            $('#goto_page_input_button').click();
        }
    });

    clickElement($('.fa'), function (e) {
        navigation($.trim($(this).attr('class')));
    });

    if ($('#search-inp input[name="search"]').length) {
        var seacrStr = $('#search-inp input[name="search"]').val();
		
		submitUrl = '?option=com_html5flippingbook&task=search&tmpl=component' + '&id=' + publicationID + (isHard ? '&hard=1' : '&hard=0') + '&text=' + seacrStr;
		
		if(window.location.href.indexOf('index.php') == -1) {
			submitUrl = 'index.php'+submitUrl;
		}
		
        $('#search-inp input[name="search"]').liveSearch({
            url: submitUrl
        });
    }

    $(document).keydown(function (e) {
        switch (e.keyCode) {
            case 37:
                $('.flipbook').turn('previous');
                return false;
                break;
            case 39:
                $('.flipbook').turn('next');
                return false;
                break;
        }
    });

    // Slider
    $("#slider").slider({
        min: 1,
        start: function (event, ui) {
            if (!window._thumbPreview) {
                _thumbPreview = $('<div />', {'class': 'thumbnail'}).html('<div></div>');
                setPreview(ui.value);
                _thumbPreview.appendTo($(ui.handle));
            } else
                setPreview(ui.value);

            moveBar(false);
        },

        slide: function (event, ui) {
            setPreview(ui.value);
        },

        stop: function () {
            if (window._thumbPreview)
                _thumbPreview.removeClass('show');

            $('.flipbook').turn('page', Math.max(1, $(this).slider('value') * 2 - 2));
        }
    });

});

function setSliderMaxViews(book) {
    numberOfViews = parseInt( book.turn('pages') % 2 == 0 ? book.turn('pages') / 2 + 1 : (book.turn('pages') + 1) / 2);
    $('#slider').slider('option', {max: numberOfViews});
}

function getViewNumber(book, page) {
    return parseInt((page || book.turn('page')) / 2 + 1, 10);
}

function moveBar(yes) {
    if (Modernizr && Modernizr.csstransforms) {
        $('#slider .ui-slider-handle').css({zIndex: yes ? -1 : 10000});
    }
}

function setPreview(view) {
    if (sliderThumbs) {
        var previewWidth = 114,
            previewHeight = 73,
            pagesCount = $('.flipbook').turn('pages');
        preview = $(_thumbPreview.children(':first'));

        if (pagesCount % 2 != 0)
            width = (view == 1) ? previewWidth / 2 : previewWidth;
        else
            width = (view == 1 || view == $('#slider').slider('option', 'max') ) ? previewWidth / 2 : previewWidth;
    }
    else {
        var width = ($('#slider').slider('option', 'max') > 49 ? 55 : 45),
            previewHeight = 25,
            preview = $(_thumbPreview.children(':first'));
    }

    _thumbPreview.
        addClass('no-transition').
        css({width: width + 15,
            height: previewHeight + 15,
            top: -previewHeight - 30,
            left: ($($('#slider').children(':first')).width() - width - 15) / 2
        });

    preview.css({
        width: width,
        height: previewHeight
    });

    if (sliderThumbs) {
        if (preview.css('background-image') === '' ||
            preview.css('background-image') == 'none') {

            preview.css({backgroundImage: 'url(' + previewSrc + ')'});

            setTimeout(function () {
                _thumbPreview.removeClass('no-transition');
            }, 0);

        }

        preview.css({backgroundPosition: '0px -' + ((view - 1) * previewHeight) + 'px'});
    }
    else {
        if (view == 1){
            echopage = view;
        }
        else if (view == $('#slider').slider('option', 'max') && $('.flipbook').turn('pages') % 2 == 0) {
            echopage = (view * 2 - 1);
        }
        else {
            echopage = (view * 2 - 2) + '-' + (view * 2 - 1);
        }

        preview.html('<b>' + echopage + '</b>');
    }
}

function zoomOutButton(show) {
    var $zoomOutICO = $('.zoom-out');
    if (!$zoomOutICO.length) {
        var $zoomOutICO = $('<i />', {'class': 'tbicon zoom-out'})
            .insertAfter($('#mainFlipBookDiv'))
            .mouseover(function() {
                $zoomOutICO.addClass('zoom-out-hover');
            })
            .mouseout(function() {
                $zoomOutICO.removeClass('zoom-out-hover');
            })
            .click(function() {
                var currPage = $('.flipbook').turn('page');
                var cntPages = $('.flipbook').turn('pages');
                if (typeof imgLink=='object' && ((imgLink.hasOwnProperty(currPage) && imgLink[currPage].hasOwnProperty('large') && imgLink[currPage].hasOwnProperty('small')) &&
                    (currPage == cntPages || (imgLink.hasOwnProperty(currPage+1) && imgLink[currPage+1].hasOwnProperty('large') && imgLink[currPage+1].hasOwnProperty('small'))))) {
                    $('#mainFlipBookDiv').zoom('zoomOut');
                }
                else {
                    zoomOutText();
                }
                $zoomOutICO.hide();
            });
    }
    //Fix for modal window
    $('#emailModal').hide();
    $('.html5fb-overlay').hide();

    $zoomOutICO.css({display: (show) ? '' : 'none'});
  }

function loadLargePage(page, pageElement, imgSRC) {
    var img = $('<img />');

    img.load(function() {
        var prevImg = pageElement.find('img');
        $(this).css({width: '100%', height: '100%'});
        $(this).appendTo(pageElement);
        prevImg.remove();
    });

    img.attr('src', imgSRC);
}

function loadSmallPage(page, pageElement, imgSRC) {
    var img = pageElement.find('img');
    img.css({width: '100%', height: '100%'});
    img.unbind('load');
    img.attr('src', imgSRC);
}

function zoomTo(event) {
    var currPage = $('.flipbook').turn('page');
    var cntPages = $('.flipbook').turn('pages');
    if (typeof imgLink=='object' && ((imgLink.hasOwnProperty(currPage) && imgLink[currPage].hasOwnProperty('large') && imgLink[currPage].hasOwnProperty('small')) &&
        (currPage == cntPages || (imgLink.hasOwnProperty(currPage+1) && imgLink[currPage+1].hasOwnProperty('large') && imgLink[currPage+1].hasOwnProperty('small')))) && !isHard) {
        if ($(this).zoom('value') == 1) {
            $(this).zoom('zoomIn', event);
        }
        else {
            $(this).zoom('zoomOut');
        }


    }
    else {
        if ($('.flipbook').data().zoomIn)
            zoomOutText();
        else if (event.target) {
            zoomInText(event, false);
        }
    }
}

function point2D(x, y) {
    return {x: x, y: y};
}

function zoomInText(event, full) {
    var flipPos, offsetLeft = 0,
        invz = 1 / 2.4,
        elem = $(event.target),
        $mainContainer = $('#mainFlipBookDiv'),
        flip = $('.flipbook'),
        dir = flip.turn('direction'),
        flipOffset = flip.offset(),
        thisOffset = elem.offset(),
        flipSize = {height: flip.height()},
        view = flip.turn('view');

    if (!view[0]) {
        flipSize.width = flip.width() / 2;
        offsetLeft = (dir == 'ltr') ? flipSize.width : 0;
        flipPos = point2D(
            (dir == 'ltr') ? flipOffset.left - thisOffset.left + flipSize.width : flipOffset.left - thisOffset.left,
            flipOffset.top - thisOffset.top
        );

    } else if (!view[1]) {
        flipSize.width = flip.width() / 2;
        offsetLeft = (dir == 'ltr') ? 0 : flipSize.width;
        flipPos = point2D(
            (dir == 'ltr') ? flipOffset.left - thisOffset.left : flipOffset.left - thisOffset.left + flipSize.width,
            flipOffset.top - thisOffset.top
        );
    } else {
        flipSize.width = flip.width();
        flipPos = point2D(
            flipOffset.left - thisOffset.left,
            flipOffset.top - thisOffset.top
        );
    }

    var bound = {pos: flipPos, size: flipSize};

    var flipPos = bound.pos,
        zoom = 2.4,
        center = point2D(bound.size.width / 2, bound.size.height / 2),
        transitionEnd = $.cssTransitionEnd(),
        autoCenter = flip.data().opts.autoCenter,
        completeTransition = function () {
            $mainContainer.unbind(transitionEnd);

            if (flip.data().zoomIn) {
                $('body').css({'overflow': 'visible'});
            }
        };

    flip.data().noCenter = true;

    if (typeof(event) != 'undefined') {
        if ('x' in event && 'y' in event) {
            pos = point2D(event.x - flipPos.x, event.y - flipPos.y);
        } else {
            pos = ($.isTouch) ?
                point2D(
                    event.originalEvent.touches[0].pageX - flipPos.x - thisOffset.left,
                    event.originalEvent.touches[0].pageY - flipPos.y - thisOffset.top
                )
                :
                point2D(
                    event.pageX - flipPos.x - thisOffset.left,
                    event.pageY - flipPos.y - thisOffset.top
                );
        }
    }
    else {
        pos = point2D(center.x, center.y);
    }

    if (pos.x < 0 || pos.y < 0 || pos.x > bound.width || pos.y > bound.height) {
        pos.x = center.x;
        pos.y = center.y;
    }

    var compose = point2D(
            (pos.x - center.x) * zoom + center.x,
            (pos.y - center.y) * zoom + center.y
        ),
        move = point2D(
            (bound.size.width * zoom > elem.width()) ? pos.x - compose.x : 0,
            (bound.size.height * zoom > elem.height()) ? pos.y - compose.y : 0
        ),
        maxMove = point2D(
            Math.abs(bound.size.width * zoom - elem.width()),
            Math.abs(bound.size.height * zoom - elem.height())
        ),
        minMove = point2D(
            Math.min(0, bound.size.width * zoom - elem.width()),
            Math.min(0, bound.size.height * zoom - elem.height())
        ),
        realPos = point2D(
            center.x * zoom - center.x - flipPos.x - move.x,
            center.y * zoom - center.y - flipPos.y - move.y
        );

    if (realPos.y > maxMove.y)
        move.y = realPos.y - maxMove.y + move.y;
    else if (realPos.y < minMove.y)
        move.y = realPos.y - minMove.y + move.y;

    if (realPos.x > maxMove.x) {
        move.x = realPos.x - maxMove.x + move.x;
    } else if (realPos.x < minMove.x) {
        move.x = realPos.x - minMove.x + move.x;
    }

    realPos = point2D(
        center.x * zoom - center.x - flipPos.x - move.x,
        center.y * zoom - center.y - flipPos.y - move.y
    );

    if (full) {
        $('#zoom-viewport').css('margin', 0);
        $mainContainer.css({width: maxMove.x, height: maxMove.y});
        move = {
          x: -maxMove.x/2,
          y: -maxMove.y/2
        };
    }

    flip.data().zoomIn = true;

    flip.turn('disable', true);

    $(window).resize(zoomOutText);

    $('#slider-bar, #page-bar, .fb_topBar').hide();

    $mainContainer.css({overflow: 'visible'});

    var has3d = 'WebKitCSSMatrix' in window || 'MozPerspective' in document.body.style;
    $mainContainer.transform((has3d) ? 'translate3d(' + move.x + 'px,' + move.y + 'px, 0px) scale3d(' + zoom + ', ' + zoom + ', 1)'  : ' translate(' + move.x + 'px, ' + move.y + 'px) scale(' + zoom + ')');

    zoomOutButton(true);

    if (transitionEnd) {
        $mainContainer.bind(transitionEnd, completeTransition);
    } else {
        setTimeout(completeTransition, 1000);
    }
}

function zoomOutText() {
    var transitionEnd = $.cssTransitionEnd(),
        completeTransition = function(e) {
            $('#mainFlipBookDiv').unbind(transitionEnd);
            $('.flipbook').turn('disable', false);
            $('body').css({'overflow': 'hidden'});
            moveBar(false);
        };

    $('.flipbook').data().zoomIn = false;

    $(window).unbind('resize', zoomOutText);

    moveBar(true);

    $('#mainFlipBookDiv').transform('scale(1)');
    $('#slider-bar, #page-bar, .fb_topBar').show();

    if (transitionEnd) {
        if ($('#zoom-viewport').attr('style')) {
            $('#zoom-viewport').removeAttr('style');
        }
        $('#mainFlipBookDiv').css({overflow: 'hidden', width: 'auto', height: 'auto'}).bind(transitionEnd, completeTransition);
        zoomOutButton(false);
        resizeViewport();
    }
    else {
        setTimeout(completeTransition, 1000);
    }
}

// Calculate the width and height of a square within another square
function calculateBound(d) {
    var bound = {width: d.width, height: d.height};

    if (bound.width > d.boundWidth || bound.height > d.boundHeight) {
        var rel = bound.width/bound.height;

        if (d.boundWidth/rel > d.boundHeight && d.boundHeight*rel <= d.boundWidth) {
            bound.width = Math.round(d.boundHeight*rel);
            bound.height = d.boundHeight;
        }
        else {
            bound.width = d.boundWidth;
            bound.height = Math.round(d.boundWidth/rel);
        }
    }

    return bound;
}

// Set the width and height for the viewport
function resizeViewport_Tablet() {
    var width = $(window).width(),
        height = $(window).height(),
        options = $('.flipbook').turn('options');

    $('.flipbook').removeClass('animated');

    $('#mainFlipBookDiv').css({
        width: width,
        height: height
    }).zoom('resize');


    if ($('.flipbook').turn('zoom') == 1) {
        var bound = calculateBound({
            width: options.width,
            height: options.height,
            boundWidth: Math.min(options.width, width),
            boundHeight: Math.min(options.height, height)
        });

        if (bound.width % 2 !== 0)
            bound.width -= 1;

        if (bound.width != $('.flipbook').width() || bound.height != $('.flipbook').height()) {
            $('.flipbook').turn('size', bound.width, bound.height);

            if ($('.flipbook').turn('page') == 1)
                $('.flipbook').turn('peel', 'br');
        }

        if (isHard) {
            var resized = false;
            $(".flipbook").on("end", function(event, pageObject, turned) {
                if (turned) {
                    resizeHardBookPage(bound);
                    resized = true;
                }
            });

            if (!resized) {
                resizeHardBookPage(bound);
            }
        }

        $('.flipbook').css({top: -(bound.height) / 2, left: -bound.width / 2});

        var pageBarWidth = $('#page-bar').outerWidth(true);
        $('#slider-bar').css({left: (pageBarWidth < 100 ? 160 : pageBarWidth + 50), maxWidth: $('.tablet-bottomBar').width() - $('#page-bar').outerWidth(true) - 80});
    }

    $('.flipbook').addClass('animated');
}

function resizeHardBookPage(bound) {
    var initialBackgrWidth = 1919;
    var pageW = $('.flipbook').width()/2;
    var backgrWidthScale = 480/pageW;
    var newBackgrWidth = parseInt(1919/backgrWidthScale);
    var cntPages = parseInt($('.flipbook').turn('pages'));

    //Set background size and background position for hard cover page
    $('.p1').css({backgroundSize: newBackgrWidth + 'px ' + bound.height + 'px'});
    $('.p2').css({backgroundSize: newBackgrWidth + 'px ' + bound.height + 'px', backgroundPosition: -pageW + 'px 0'});
    $('.p' + cntPages).css({backgroundSize: newBackgrWidth + 'px ' + bound.height + 'px', backgroundPosition: -(pageW*3) + 'px 0'});
    $('.p' + (cntPages - 1)).css({backgroundSize: newBackgrWidth + 'px ' + bound.height + 'px', backgroundPosition: -(pageW*2) + 'px 0'});

    var innerPageW = pageW/1.043;
    var innerPageH = bound.height/1.031;

    $('.page:not(.hard)').parent().parent().css({top: 9, width: innerPageW, height: innerPageH});
    $('.page:not(.hard)').parent().next().css({width: innerPageW, height: innerPageH});
    $('.page:not(.hard, .odd)').css({width: innerPageW, height: innerPageH}).parent().parent().css({left: pageW - innerPageW, right: 'auto'});
    $('.page:not(.hard, .even)').css({width: innerPageW, height: innerPageH}).parent().parent().css({right: pageW - innerPageW, left: 'auto'});

    $('.flipbook-page').css({width: innerPageW - 80, height: innerPageH - 80, overflow: 'hidden'});

    $('.depth').css('background-size', 'auto ' + bound.height/1.017 + 'px');
}

function fbTurningPage(book, page, pages, slider_one_page_left, slider_tho_page_left, isTablet) {
    if (page == 1) {
        if (!isTablet) {
            $('#slider-bar').css('left', slider_one_page_left + 'px');
        }
        $('#fb_bookname').hide();
        $('#search-inp').hide();
        $('#page-bar').hide();
        $('.tb_social').css({'float': 'right', 'margin-left': '0'});
    }
    else {
        if (page != pages) {
            if (!isTablet) {
                $('#slider-bar').css('left', slider_tho_page_left + 'px');
            }
            $('#fb_bookname').show();
            $('#search-inp').show();
            $('#page-bar').show();
            $('.tb_social').css({'float': 'right', 'margin-left': '0'});
        }
        else {
            if (!isTablet) {
                $('#slider-bar').css('left', '-80px');
            }
            $('#fb_bookname').hide();
            $('#search-inp').hide();
            $('#page-bar').hide();
            $('.tb_social').css({'float': 'left', 'margin-left': '200px'});
        }
    }

    if (isHard) {
        if (page > 3 && page < pages - 3) {
            if (page == 1) {
                book.turn('page', 2).turn('stop').turn('page', page);
                e.preventDefault();
                return;
            }
            else if (page == pages) {
                book.turn('page', pages - 1).turn('stop').turn('page', page);
                e.preventDefault();
                return;
            }
        }
        else if (page > 3 && page < pages - 3) {
            if (currentPage == 1) {
                book.turn('page', 2).turn('stop').turn('page', page);
                e.preventDefault();
                return;
            }
            else if (currentPage == pages) {
                book.turn('page', pages - 1).turn('stop').turn('page', page);
                e.preventDefault();
                return;
            }
        }

        updateDepth(book, page);

        if (page >= 2) {
            $('.flipbook .p2').addClass('fixed');
        }
        else {
            $('.flipbook .p2').removeClass('fixed');
        }

        if (page < book.turn('pages')) {
            $('.flipbook .p' + (pages - 1)).addClass('fixed');
        }
        else {
            $('.flipbook .p' + (pages - 1)).removeClass('fixed');
        }
    }
}

function addPage(page, book) {
    var id, pages = book.turn('pages');

    var element = $('<div />',
        {'class': 'own-size',
            css: {width: 460, height: 582}
        }).
        html('<div class="loader"></div>');
}

function updateDepth(book, newPage) {
    var page = book.turn('page'),
        pages = book.turn('pages'),
        depthWidth = Math.round(16*Math.min(1, page*2/pages));

    newPage = newPage || page;

    if (newPage>3) {
        $('.flipbook .p2 .depth').css({
            width: depthWidth,
            left: 20 - depthWidth
        });
    }
    else {
        $('.flipbook .p2 .depth').css({width: 0});
    }

    depthWidth = Math.round(16*Math.min(1, (pages-page)*2/pages));

    if (newPage < pages - 3) {
        $('.flipbook .p' + (pages - 1) + ' .depth').css({
            width: depthWidth,
            right: 20 - depthWidth
        });
    }
    else {
        $('.flipbook .p' + (pages - 1) + ' .depth').css({width: 0});
    }
}

function isChrome() {
    return navigator.userAgent.indexOf('Chrome/19') !=- 1 ||
        navigator.userAgent.indexOf('Chrome/20') !=- 1;
}
