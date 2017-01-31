var page = 1, maxPage = 1;
var settings = '', storedPage = '';
var defBackgr = '#f9f9f9', defFontColor = '#000';
var changeBackgr = '', changeFontColor = '';

if (!user) {
    //Get stored settings from cookie
    $.cookie.json = true;
    settings = $.cookie('_reader-settings:guest');
    storedPage = $.cookie('_pub-page:guest');
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

$(window).on("orientationchange", function () {
    var $pageContent = $('#page');
    var pageWidth = $('#page-content').width();
    $pageContent.find('img').css('max-width', pageWidth);
    $pageContent.find('video').css('max-width', pageWidth);
});

/* Settings page - Set font size */
$(document).on("pageinit", "#settings", function () {
    //Store settings into cookie, when we close settings page
    $('.ui-icon-delete').on('click', function () {
        settings = {
            showNavi: ($('input[name="navi-buttons"]').is(':checked') ? 1 : 0),
            nightMode: ($('input[name="night-mode"]').is(':checked') ? 1 : 0),
            backColor: $('input[name="backgroundcolor"]').val(),
            fontColor: $('input[name="fontcolor"]').val(),
            fontSize: parseInt($('input[name="fontsize"]').val())
        };

        if (!user) {
            $.cookie('_reader-settings:guest', settings, {expires: 365, path: '/'});
        }
        else {
            //Save user settings
            $.ajax({
                type: "POST",
                url: realUrl + "index.php?option=com_html5flippingbook&task=userSettings&tmpl=component",
                data: 'pubID=' + pubID + '&settings=' + JSON.stringify(settings),
                dataType: 'JSON'
            });
        }
    });

    var $fontSizeEl = $('input[name="fontsize"]');
    var fontSizeVal = 12;
    if (typeof settings != 'undefined' && settings.fontSize) {
        fontSizeVal = settings.fontSize;
    }
    $fontSizeEl.val(fontSizeVal).slider("refresh");
    $fontSizeEl.on("slidestop", function () {
        $('#page').attr('style', 'font-size: ' + $(this).val() + 'px');
    });
});

$(document).ready(function () {
    var $naviButtonsEl  = $('input[name="navi-buttons"]');
    var $nightModeEl    = $('input[name="night-mode"]');
    var $backColorEl    = $('input[name="backgroundcolor"]');
    var $fontColorEl    = $('input[name="fontcolor"]');
    var $fontSizeEl     = $('input[name="fontsize"]');
    var $pageContent    = $('#page');

    if (user) {
        //Get user settings for reader
        $.ajax({
            type: "GET",
            url: realUrl + "index.php?option=com_html5flippingbook&task=userSettings&tmpl=component",
            data: 'pubID=' + pubID,
            dataType: 'JSON',
            async: false,
            success: function (data) {
                settings = JSON.parse(data.settings);
                storedPage = data.storedPage;
                page = storedPage.openPage;
                maxPage = storedPage.lastPage;
            }
        });

        var examTimer = new Countdown({
            seconds: 90,  // number of seconds to count down
            onCounterEnd: function(){
                $.ajax({
                    type: "POST",
                    url: realUrl + "index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component",
                    data: 'pubID=' + pubID + '&action=updateSpendTime&sec=90',
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
            url: realUrl + "index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component",
            data: 'pubID=' + pubID + '&action=lastopen',
            dataType: 'JSON'
        });
    }

    //Open the book on the last page, where the user read
    if (typeof storedPage != 'undefined' && storedPage.publication == pubID && !user) {
        page = storedPage.openPage;
        maxPage = storedPage.lastPage;
    }

    getPage(page);

    //Set stored settings to book
    if (typeof settings != 'undefined') {
        if (settings.showNavi) {
            $('#navi').show();
            $naviButtonsEl.attr('checked', true);
        }

        if (settings.nightMode) {
            $('#page-container').css('background-color', '#000');
            $('#page-content').css('background-color', '#000');
            $pageContent.css('color', '#FFF');
            $nightModeEl.attr('checked', true);
        }

        changeBackgr = settings.backColor;
        changeFontColor = settings.fontColor;

        if (!settings.nightMode && changeBackgr != '') {
            $('#page-container').css('background-color', changeBackgr);
            $('#page-content').css('background-color', changeBackgr);
            $backColorEl.val(changeBackgr);
        }

        if (!settings.nightMode && changeFontColor != '') {
            $pageContent.css('color', changeFontColor);
            $fontColorEl.val(changeFontColor);
        }

        $pageContent.attr('style', 'font-size: ' + settings.fontSize + 'px');
    }

    //--- Settings ----------------------------------------------//
    $fontColorEl.minicolors({
        position: 'top right'
    });

    $backColorEl.minicolors({
        position: 'bottom right'
    });

    $naviButtonsEl.on('change', function () {
        if (this.checked) {
            $('#navi').show();
        }
        else {
            $('#navi').hide();
        }
    });

    $nightModeEl.on('change', function () {
        if (this.checked) {
            $('#page-container').css('background-color', '#000');
            $('#page-content').css('background-color', '#000');
            $pageContent.css('color', '#FFF');
        }
        else {
            defBackgr    = changeBackgr != '' ? changeBackgr : defBackgr;
            defFontColor = changeFontColor != '' ? changeFontColor : defFontColor;

            $('#page-container').css('background-color', defBackgr);
            $('#page-content').css('background-color', defBackgr);
            $pageContent.css('color', defFontColor);
        }
    });

    $backColorEl.on('change', function () {
        var backColor = $(this).val();
        changeBackgr = backColor;
        $('#page-container').css('background-color', backColor);
        $('#page-content').css('background-color', backColor);
    });

    $fontColorEl.on('change', function () {
        var fontColor = $(this).val();
        changeFontColor = fontColor;
        $pageContent.css('color', fontColor);
    });
    //-------------------------------------------------------------//

    $("#page-header").toolbar("hide");
    $("#page-footer").toolbar("hide");

    $('input[name="page-slider"]').on("slidestop", function () {
        page = jQuery(this).val();
        getPage(page);
    });

    $(document).on({
        swipeleft: function () {
            getPage(page++);
        },
        swiperight: function () {
            getPage(page--);
        }
    }, '#page-container');

    //--- Navi buttons -----------------//
    $('#prev').on('tap', function () {
        getPage(page--);
    });

    $('#next').on('tap', function () {
        getPage(page++);
    });
    //-----------------------------------//
});

getPage = function (pageN) {
    if (page > maxPage || page == 0) {
        page = (page == 0 ? 1 : maxPage);
        return;
    }

    var $pageContent    = $('#page');
    var $slider = $('input[name="page-slider"]');
    $slider.val(page).slider("refresh"); //Set current page

    $.ajax({
        type: "GET",
        url: realUrl + "index.php?option=com_html5flippingbook&task=getPageContent&tmpl=component",
        data: "pubID=" + pubID + "&page=" + page,
        dataType: 'JSON',
        success: function (data) {
            maxPage = data.lastPage;

            if (!user) {
                storedPage = {
                    publication: pubID,
                    openPage: page,
                    lastPage: maxPage
                };

                $.cookie('_pub-page:guest', storedPage, {expires: 365, path: '/'});
            }
            else {
                //Update last open page
                $.ajax({
                    type: "POST",
                    url: realUrl + "index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component",
                    data: 'pubID=' + pubID + '&action=updatePage&page=' + page,
                    dataType: 'JSON'
                });
            }

            $slider.attr('max', maxPage);

            $('#book-title').text(data.title); //Set book title
            $('#book-author').text(data.author); //Set book author
            $pageContent.empty().append(data.content); //Set page content

            var pageWidth = $('#page-content').width();
            $pageContent.find('img').css('max-width', pageWidth);
            $pageContent.find('video').css('max-width', pageWidth);
        }
    });
};
