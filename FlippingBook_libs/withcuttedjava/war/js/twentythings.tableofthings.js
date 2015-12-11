// Copyright 2011 Google Inc.
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
// USA.

/**
 * @fileoverview Controls the an hiding of the Table of Things view as well as
 * any interaction and navigation therein.
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.tableofthings = {};


/**
 * The number of columns which the table should be built out of.
 */
TT.tableofthings.COLUMNS = 5;


/**
 * Flags if the table of things view is currently being shown.
 */
TT.tableofthings.visible = false;


/**
 * Initialize table of things class.
 */
TT.tableofthings.initialize = function() {

  // Register event listeners.
  $('#table-of-contents ul li').mouseenter(TT.tableofthings.onChapterMouseOver);
  $('#table-of-contents ul li').mouseleave(TT.tableofthings.onChapterMouseOut);
  $('#table-of-contents ul li').click(TT.tableofthings.onChapterClick);

};


/**
 * Updates the visual markers that indicate which chapters that have already
 * been read.
 */
TT.tableofthings.updateReadMarkers = function() {
  $('#table-of-contents ul li').each(function() {
    var articleId = $('a', this).attr('data-article');

    if (TT.storage.hasArticleBeenRead(articleId)) {
      $(this).addClass('read');
    }
  });
};


/**
 * Updates the selection in the Table of Things. By default, this will select
 * the item that corresponds to the currently selected page.
 * @param {string} overrideArticleId If this is specified, the item that
 *     corresponds to this ID will be selected.
 */
TT.tableofthings.updateSelection = function(overrideArticleId) {

  // Fetch the article name of the currently selected page.
  var selectedArticleId =
      TT.navigation.classToArticle($('#pages section.current').attr('class'));

  // If an override article ID is specified, use that instead of the current
  // page.
  if (overrideArticleId) {
    selectedArticleId = overrideArticleId;
  }

  // Remove selection from all elements.
  $('#table-of-contents ul li').removeClass('selected');

  // If the selected article is valid, find the corresponding element and
  // select it.
  if (selectedArticleId) {
    var element = $('#table-of-contents ul li')
        .find('[data-article*=' + selectedArticleId + ']');

    if (element && element.parent()) {
      element.parents('li').addClass('selected');
    }
  }
};


/**
 * Show table of things.
 */
TT.tableofthings.show = function() {
  if (!TT.tableofthings.visible) {
    $('body').addClass('tot');

    // Fade in the entire component.
    $('#table-of-contents').stop(true, true).show().fadeTo(200, 1);

    // Make sure the header is fully visible (it fades out when hidden).
    $('#table-of-contents div.header').stop().css({
      opacity: 1
    });

    // Truncate titles too long.
    TT.tableofthings.truncate();

    // Fade in each item in a wave-like animation.
    $('#table-of-contents ul li').each(function(i) {
      var row = Math.floor(i / TT.tableofthings.COLUMNS);
      var col = i % TT.tableofthings.COLUMNS;

      row++; // Make 1 indexed.
      col++;

      $(this).stop().css({ opacity: 0 }).show().delay((row + col) * 50)
          .fadeTo(100, 1);
    });

    // Force the layout to update now that the dimensions of the component can
    // be accessed.
    TT.updateLayout();
  }

  TT.tableofthings.visible = true;

  TT.pageflip.unregisterEventListeners();
};


/**
 * Hide table of things.
 */
TT.tableofthings.hide = function() {
  $('body').removeClass('tot');

  // Fade out the entire component.
  $('#table-of-contents').delay(200).fadeTo(200, 0, function() {
    $(this).hide();
  });

  // Fade out the header faster than the component.
  $('#table-of-contents div.header').stop().fadeTo(150, 0);

  var length = $('#table-of-contents ul li').length;

  // Fade out each item in a wave-like animation.
  $('#table-of-contents ul li').each(function(i) {
    var row = Math.floor((length - 1 - i) / TT.tableofthings.COLUMNS);
    var col = (length - 1 - i) % TT.tableofthings.COLUMNS;

    row++; // Make 1 indexed.
    col++;

    $(this).stop().fadeTo((row + col) * 40, 0);
  });

  TT.tableofthings.visible = false;

  if (!TT.navigation.isFullScreen()) {
    TT.pageflip.registerEventListeners();
  }

  TT.updateLayout();
};


/**
 * On click table of things chapter.
 * @param {Object} event Event object.
 * @return {boolean} Return false.
 */
TT.tableofthings.onChapterClick = function(event) {
  if ($('body').hasClass('tot')) {
    var articleId = $(event.target).parents('li').children('a')
        .attr('data-article');

    if (!articleId) {
      articleId = $(event.target).children('a').attr('data-article');
    }

    if (TT.navigation.goToPage(articleId, 1)) {
      TT.tableofthings.hide();

      TT.chapternav.updateSelection(articleId);
      TT.tableofthings.updateSelection(articleId);
    }
  }

  return false;
};


/**
 * Show extended/truncated titles.
 * @param {Object} event Event object.
 * @return {boolean} Return false.
 * @this {Object} Event target.
 */
TT.tableofthings.onChapterMouseOver = function(event) {
  $(this).find('.extended').show();
  $(this).find('.ellipsis').hide();
  $(this).find('p.fullyTruncated').show();

  // If the rolled over element has the inactive or selected classes, prevent
  // the overlay from being shown.
  if ($(event.target).parents('li').hasClass('disabled') ||
      $(event.target).parents('li').hasClass('selected')) {
    return false;
  }
};


/**
 * Hide extended/truncated titles.
 * @param {Object} event Event object.
 * @this {Object} Event target.
 */
TT.tableofthings.onChapterMouseOut = function(event) {
  $(this).find('.extended').hide();
  $(this).find('.ellipsis').show();
  $(this).find('p.fullyTruncated').hide();
};


/**
 * Truncate titles/subtitles that are too long.
 */
TT.tableofthings.truncate = function() {
  $('#table-of-contents ul li').each(function(i) {
    var that = $(this);
    var thatA = that.find('a');
    that.css('z-index', 1000 - i);
    if (thatA.outerHeight() > 130) TT.tableofthings.findSliceLength(thatA);
  });
};


/**
 * Find and set truncation length for a TOT element.
 * @param {Object} aEl Element.
 */
TT.tableofthings.findSliceLength = function(aEl) {

  // First try truncating the description to reach desired height.
  var p = aEl.find('p').eq(1);
  var len = p.text().length;
  var txt = p.text();
  var start = len;
  while (aEl.outerHeight() > 130) {
    len = start = txt.lastIndexOf(' ', start - 1);
    if (len <= 0) len = 0;
    var tease = txt.slice(0, len);
    var extended = txt.slice(len);
    var ellipsis = extended.length > 0 && len != 0 ? ' ...' : '';
    p.html('<span class="tease">' + tease + '</span><span class="ellipsis">' +
        ellipsis + '</span><span class="extended" style="display:none;">' +
        extended + '</span>');
    if (len == 0) {
      p.addClass('fullyTruncated').hide();
      break;
    }
  }

  // Truncate the title if description truncation doesn't suffice.
  var h3 = aEl.find('h3');
  len = h3.text().length;
  txt = h3.text();
  var start = len;
  while (aEl.outerHeight() > 130) {
    len = start = txt.lastIndexOf(' ', start - 1);
    var tease = txt.slice(0, len);
    var extended = txt.slice(len);
    var ellipsis = extended.length > 0 ? ' ...' : '';
    h3.html('<span class="tease">' + tease + '</span><span class="ellipsis">' +
        ellipsis + '</span><span class="extended" style="display:none;">' +
        extended + '</span>');
  }
};
