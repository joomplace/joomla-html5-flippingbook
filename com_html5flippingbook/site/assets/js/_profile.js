(function ($) {
    $(function () {
        var inProgress = false;
        var activeList = 'reading';
        var $rows1Slides = '', $rows2Slides = '', slideWidth = 0;

        $('a[data-toggle="tab"]').on('shown', function (e) {
            //save the latest tab using a cookie:
            $.cookie('last_tab', $(e.target).attr('href'));
            activeList = $(e.target).attr('href').split('_')[1];

            //Enable ajax loader for content
            inProgress = false;

            //Set styles for publication slides
            if ($rows1Slides.length > 1) {
                for (var i = 1; i < $rows1Slides.length; i++) {
                    $($rows1Slides[i]).css('left', -1*slideWidth + 'px');
                }

                for (var j = 1; j < $rows2Slides.length; j++) {
                    $($rows2Slides[j]).css('left', -1*slideWidth + 'px');
                }
            }
        });

        //activate latest tab, if it exists:
        var lastTab = $.cookie('last_tab');
        if (lastTab) {
            $('ul.nav-tabs').children().removeClass('active');
            $('a[href=' + lastTab + ']').parents('li:first').addClass('active');
            $('div.tab-content').children().removeClass('active');
            $(lastTab).addClass('active');
            activeList = lastTab.split('_')[1];
        }
        else {
            $('#viewTabs a:first').tab('show'); //Show first tab
        }

        /*Publication slider functionality*/
        $rows1Slides = $('#tab_' + activeList + ' .row-1 .loc');
        $rows2Slides = $('#tab_' + activeList + ' .row-2 .loc');
        slideWidth   = parseInt($rows1Slides.actual('width'));

        var $firstSlideRow1 = $rows1Slides.first();
        var $firstSlideRow2 = $rows2Slides.first();
        var $lastSlideRow1  = $rows1Slides.last();
        var $lastSlideRow2  = $rows2Slides.last();

        if ($rows1Slides.length > 1) {
            //Set styles for publication slides
            for (var i = 1; i < $rows1Slides.length; i++) {
                $($rows1Slides[i]).css('left', -1 * slideWidth + 'px');
            }

            for (var j = 1; j < $rows2Slides.length; j++) {
                $($rows2Slides[j]).css('left', -1 * slideWidth + 'px');
            }

            $('#tab_' + activeList + ' .slide-navi').show();

            $('.htmlfb5-sl-next').on('click', function() {
                $firstSlideRow1.animate({left: slideWidth}, 200, function () {
                    $lastSlideRow1.animate({left: 0}, 200, function() {
                        $firstSlideRow1.before($lastSlideRow1);

                        //Update vars when we change slides ordering
                        $firstSlideRow1 = $('#tab_' + activeList + ' .row-1 .loc:first');
                        $lastSlideRow1  = $('#tab_' + activeList + ' .row-1 .loc:last');

                        $lastSlideRow1.css('left', -1 * slideWidth);
                    });
                });
                $firstSlideRow2.animate({left: slideWidth}, 200, function () {
                    $lastSlideRow2.animate({left: 0}, 200, function() {
                        $firstSlideRow2.before($lastSlideRow2);

                        //Update vars when we change slides ordering
                        $firstSlideRow2 = $('#tab_' + activeList + ' .row-2 .loc:first');
                        $lastSlideRow2  = $('#tab_' + activeList + ' .row-2 .loc:last');

                        $lastSlideRow2.css('left', -1 * slideWidth);
                    });
                });
            });

            $('.htmlfb5-sl-prev').on('click', function () {
                $firstSlideRow1.animate({left: -1 * slideWidth}, 200, function () {
                    $lastSlideRow1.after($firstSlideRow1);

                    //Update vars when we change slides ordering
                    $firstSlideRow1 = $('#tab_' + activeList + ' .row-1 .loc:first');
                    $lastSlideRow1  = $('#tab_' + activeList + ' .row-1 .loc:last');

                    $firstSlideRow1.css('left', slideWidth).animate({left: 0}, 200);
                });
                $firstSlideRow2.animate({left: -1 * slideWidth}, 200, function () {
                    $lastSlideRow2.after($firstSlideRow2);

                    //Update vars when we change slides ordering
                    $firstSlideRow2 = $('#tab_' + activeList + ' .row-2 .loc:first');
                    $lastSlideRow2  = $('#tab_' + activeList + ' .row-2 .loc:last');

                    $firstSlideRow2.css('left', slideWidth).animate({left: 0}, 200);
                });
            });
        }
        /*End of publication slider*/

        var startRFrom = 3;
        var startFFrom = 3;

        /* Ajax loader for content when user scrolling. Can change action from scroll to $('#more').click(function())*/
        $(window).scroll(function() {
            if($(window).scrollTop() >= $('.html5fb-profile').height() - $(window).height() && !inProgress) {
                $('.content-loading').show();
                $.ajax({
                    url: 'index.php?option=com_html5flippingbook&task=getAjaxContent&tmpl=component',
                    method: 'POST',
                    data: {"start" : (activeList == 'reading' ? startRFrom : startFFrom), "list" : activeList},
                    dataType: 'HTML',
                    beforeSend: function() {
                        inProgress = true;
                    }
                }).done(function(data){
                        if (data.length > 0) {
                            $("#" + activeList + "-list").append(data);

                            inProgress = false;
                            if (activeList == 'reading') {
                                startRFrom += 3;
                            }
                            else {
                                startFFrom += 3;
                            }
                        }
                        else {
                            $.ajax({
                                url: 'index.php?option=com_html5flippingbook&task=getAjaxContent&tmpl=component',
                                method: 'POST',
                                data: {"start" : 0, "list" : activeList}
                            });
                        }
                        $('.content-loading').hide();
                    }
                );
            }
        });

        //Show "back to top" button
        $(document).on({
                mouseenter: function () {
                    $(this).find('.html5fb-top').fadeIn('fast');
                },
                mouseleave: function () {
                    $(this).find('.html5fb-top').fadeOut('fast');
                }
        }, '.html5fb-list-item');

        $('a[href="#tab_reading"], a[href="#tab_favorite"]').on('click', function () {
            var $that = $(this);

            var isBookShelfActive = $($that.attr('href') + ' > div.bookshelf').hasClass('active');
            var isListActive = $('#' + $that.attr('href').split('_')[1] + '-list').hasClass('active');

            if (isBookShelfActive) {
                $('#list').removeClass('active');
                $('#bookshelf').removeClass('active').addClass('active');
                inProgress = true;
            }
            else if (isListActive) {
                $('#bookshelf').removeClass('active');
                $('#list').removeClass('active').addClass('active');
                inProgress = false;
            }
        });

        $('#read-publ').on('click', function () {
            var activeTab = jQuery('#viewTabs > li.active > a').attr('href');
            var $that = jQuery(this);

            if ($that.hasClass('active')) {
                $that.removeClass('active');
                $(activeTab + ' .hide-publ').hide();

                if (!$('ul#' + activeTab.split('_')[1] + '-list > li:not(.hide-publ)').length) {
                    $('#' + activeTab.split('_')[1] + '-alert').show();
                }

                $that.attr('data-original-title', Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_DISPLAY_READ_PUBL'));
                $that.find('i.fa-eye').removeClass('fa-eye').addClass('fa-eye-slash');
            }
            else {
                $that.addClass('active');
                $(activeTab + ' .hide-publ').show();

                if (!jQuery('ul#' + activeTab.split('_')[1] + '-list > li:not(.hide-publ)').length) {
                    jQuery('#' + activeTab.split('_')[1] + '-alert').hide();
                }

                $that.attr('data-original-title', Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_DISPLAY_UNREAD_PUBL'));
                $that.find('i.fa-eye-slash').removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('#list, #bookshelf').on('click', function () {
            var activeTab = $('#viewTabs > li.active > a').attr('href');

            $(this).addClass('active');
            $('#' + (this.id == 'list' ? 'bookshelf' : (this.id == 'bookshelf' ? 'list' : ''))).removeClass('active');

            if (this.id == 'list') {
                $('#' + activeTab.split('_')[1] + '-list').addClass('active').show();
                $(activeTab + ' > div.bookshelf').removeClass('active').hide();
                inProgress = false;
            }
            else if (this.id == 'bookshelf') {
                $('#' + activeTab.split('_')[1] + '-list').removeClass('active').hide();
                $(activeTab + ' > div.bookshelf').addClass('active').show();
                inProgress = true;
            }
        });

        $('#jomshareModal').on('show', function () {
            $('#jomshareModal .button-block').show();

            var pubID = $('#jomshareModal input[name="publID"]').val();
            var $pubEl = $('.pub-' + pubID);
            var title = jQuery.trim(jQuery($pubEl.find('a')[0]).text());
            if (!title) {
                title = $pubEl.attr('alt');
            }
            var body = 'Dear Friend!\r\n\r\nCheck out the link below:\r\n' + liveSite + 'index.php?option=com_html5flippingbook&view=publication&id=' + pubID + '&tmpl=component';
            $('#jomshareModal .form-horizontal #subject').val(title);
            $('#jomshareModal .form-horizontal #body').val(body);
            $('#jomshareModal .form-horizontal').hide();
        });
    });
})(jQuery);

function shareAjaxAction(action) {
    var data = '';
    var pubID = parseInt(jQuery('#jomshareModal input[name="publID"]').val());
    if (!pubID) {
        alert(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_PUB_ERROR'));
        return false;
    }

    if (action == 'sendprivate') {
        if (jQuery('#jomshareModal #friends').val() == -1) {
            alert(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_JSSHARE_FRIEND_ERROR'));
            return false;
        }

        data = {
            'to': jQuery('#jomshareModal #friends').val(),
            'subject': jQuery('#jomshareModal #subject').val(),
            'body': jQuery('#jomshareModal #body').val()
        };
    }

    jQuery.ajax({
        type: 'POST',
        url: liveSite + 'index.php?option=com_html5flippingbook&task=jomShare&tmpl=component',
        data: {'pubID': pubID, 'action': action, 'data': JSON.stringify(data)},
        dataType: 'JSON',
        success: function (data) {
            if (data.error == 1) {
                alert(data.message);
                return false;
            }
            alert(data.message);
            jQuery('#jomshareModal').modal('hide');
            return true;
        }
    });

    return true;
}

function showPrivate() {
    jQuery('#jomshareModal .button-block').slideUp('fast', function () {
        jQuery('#jomshareModal .form-horizontal').slideDown('fast');
    });
}

function backToTop() {
    jQuery('html, body').animate({scrollTop: '0px'}, 800);
}

function userPublAction(publID, action, buttonPosition) {
    var availableActions = ['reading', 'favorite', 'read', 'reading_remove', 'favorite_remove', 'read_remove', 'sendtofriend', 'share'];
    var actionIcons = {reading: '.icon-list-2', favorite: '.icon-star', read: '.icon-eye-blocked'};

    if (jQuery.inArray(action, availableActions) != -1) {
        if (!user) {
            alert(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_USER'));
            return false;
        }

        if (action == 'sendtofriend') {
            jQuery('#emailModal input[name="publID"]').val(publID);
            jQuery('#emailModal').modal('show');
            return false;
        }

        if (action == 'share') {
            jQuery('#jomshareModal input[name="publID"]').val(publID);
            jQuery('#jomshareModal').modal('show');
            return false;
        }

        jQuery.ajax({
            type: 'POST',
            url: liveSite + 'index.php?option=com_html5flippingbook&task=userPublAction&tmpl=component',
            data: {'pubID': publID, 'action': action},
            dataType: 'JSON',
            success: function (data) {
                var $pubEl = jQuery('.' + buttonPosition + '-pub-' + publID);

                if (!data.error) {
                    $pubEl.find('.list-overlay').addClass('done').fadeIn(150).delay(5).fadeOut(150, function () {
                        if (action == 'reading_remove' || action == 'favorite_remove') {
                            $pubEl.slideUp('fast').remove();
                        }

                        var readEl = null, i = 0;
                        if (action == 'read') {
                            $pubEl.addClass('hide-publ').hide();

                            readEl = jQuery('a#read_' + publID);
                            for (i = 0; i < readEl.length; i++) {
                                jQuery(readEl[i]).attr('onclick', "userPublAction(" + publID + ", 'read_remove', '" + buttonPosition + "'); return false;");
                            }

                            jQuery('#read_' + publID + ' > i.fa-eye-slash').removeClass('fa-eye-slash').addClass('fa-eye');
                            jQuery('#read_' + publID + ' > #text_' + publID).text(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_REMOVE_READ'))
                        }
                        else if (action == 'read_remove') {
                            $pubEl.removeClass('hide-publ');

                            readEl = jQuery('a#read_' + publID);
                            for (i = 0; i < readEl.length; i++) {
                                jQuery(readEl[i]).attr('onclick', "userPublAction(" + publID + ", 'read', '" + buttonPosition + "'); return false;");
                            }

                            jQuery('a#read_' + publID + ' > i.fa-eye').removeClass('fa-eye').addClass('fa-eye-slash');
                            jQuery('a#read_' + publID + ' > #text_' + publID).text(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_READ_TIP'))
                        }

                        if (!jQuery('ul#' + buttonPosition + '-list > li:not(.hide-publ)').length) {
                            jQuery('#' + buttonPosition + '-alert').show();
                        }
                    });
                }
                else if (data.error == 1) {
                    $pubEl.find('.list-overlay').addClass('error').fadeIn(150).delay(5).fadeOut(150, function () {
                        alert(data.message);
                    });

                    return false;
                }

                return true;
            }
        });
    }
    else {
        alert(Joomla.JText._('COM_HTML5FLIPPINGBOOK_FE_ACTION_ERROR_ACTION'));
        return false;
    }

    return true;
}