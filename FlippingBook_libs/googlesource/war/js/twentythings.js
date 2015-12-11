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
 * @fileoverview Main class includes initializers, constants, global handlers /
 * utilities, and layout update functions.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 */


/**
 * Global namespace.
 * @type {Object}
 */
var TT = TT || {};


/**
 * The width of one page (excluding jacket) in the book.
 * @type {number}
 */
TT.PAGE_WIDTH = 800;


/**
 * The height of one page (excluding jacket) in the book.
 * @type {number}
 */
TT.PAGE_HEIGHT = 500;


/**
 * Minimum width of the whole app (when scaled to be smaller than this,
 * scrollbars will appear).
 * @type {number}
 */
TT.PAGE_MIN_WIDTH = 1000;


/**
 * Minimum width of the whole app (when scaled to be smaller than this,
 * scrollbars will appear).
 * @type {number}
 */
TT.PAGE_MIN_HEIGHT = 680;


/**
 * Inner margin (x) of the book (space between where the book jacket and white
 * paper).
 * @type {number}
 */
TT.PAGE_MARGIN_X = 32;


/**
 * Inner margin (y) of the book (space between where the book jacket and white
 * paper).
 * @type {number}
 */
TT.PAGE_MARGIN_Y = 10;


/**
 * The total width of the book, including jacket.
 * @type {number}
 */
TT.BOOK_WIDTH = 1660;


/**
 * The total width of the book, including jacket.
 * @type {number}
 */
TT.BOOK_HEIGHT = 520;


/**
 * The width of the closed book, including jacket.
 * @type {number}
 */
TT.BOOK_WIDTH_CLOSED = TT.BOOK_WIDTH / 2;


/**
 * An offset applied to the horizontal positioning of the book (#book).
 * @type {number}
 */
TT.BOOK_OFFSET_X = 5;


/**
 * User agent.
 * @type {string}
 */
TT.UA = navigator.userAgent.toLowerCase();


/**
 * Whether UA is a touch device.
 * @type {boolean}
 */
TT.IS_TOUCH_DEVICE = TT.UA.match(/android/) || TT.UA.match(/iphone/) ||
    TT.UA.match(/ipad/) || TT.UA.match(/ipod/);


/**
 * Initiates the main application logic. This is the first point at which any
 * scripting logic is applied.
 */
TT.initialize = function() {

  TT.preloader.initialize();

  // Initialize managers, do not alter the order in which these are called.
  TT.overlay.initialize();
  TT.storage.initialize();
  TT.cache.initialize();
  TT.search.initialize();
  TT.chapternav.initialize();
  TT.sharing.initialize();
  TT.paperstack.initialize();
  TT.tableofthings.initialize();
  TT.flipintro.initialize();

  // Register event listeners.
  $(window).resize(TT.onWindowResize);
  $(window).scroll(TT.onWindowScroll);

  // Trigger an initial update of the layout.
  TT.updateLayout();

  // Prevent native drag and drop behavior of all images. This is important
  // since it is very easy to start dragging assets by mistake while trying to
  // flip pages.
  $('img').mousedown(function(event) { event.preventDefault() });
};


/**
 * Called when the contents of the application has finished loading and we are
 * ready to show the book.
 */
TT.startup = function() {

  // Initialize the managers which have depenencies on content being loaded.
  TT.navigation.initialize();
  TT.pageflip.initialize();
  TT.history.initialize();
  TT.locale.initialize();

  // Update the navigation selections.
  TT.chapternav.updateSelection();
  TT.tableofthings.updateSelection();

  // Update which chapters are marked as read.
  TT.chapternav.updateReadMarkers();
  TT.tableofthings.updateReadMarkers();

  // Update the paper stack to match the current page.
  TT.paperstack.updateStack();

  // Make sure the pagination is up to date with the current page.
  TT.navigation.updateNextPrevLinks($('#pages section.current'));

};


/**
 * Event handler for window.onresize, results in an update of the layout.
 * @param {Object} event Resize event object.
 */
TT.onWindowResize = function(event) {
  TT.updateLayout();
};


/**
 * Event handler for window.scroll, results in an update to certain parts of the
 * layout.
 * @param {Object} event Scroll event object.
 */
TT.onWindowScroll = function(event) {
  TT.updateLayout(true);
};


/**
 * Updates the layout of all elements that require JS controlled positioning.
 * This is typically elements that are centered but with limits on min and max
 * positions.
 *
 * Note that most of these elements will originally be positioned entirely via
 * CSS. JS control over the positioning is especially important for resizing
 * logic, explicit control of overflows, centering etc.
 *
 * @param {boolean} fromScroll Flags if this update to the layout originates
 *     from the application being scrolled.
 */
TT.updateLayout = function(fromScroll) {
  // Fetch the application size
  var applicationSize = {
    width: $(window).width(),
    height: $(window).height()
  };

  // If we are not below the minimum size of the app, overflow should always be
  // hidden.
  $('body').css({
    overflowX: applicationSize.width < TT.PAGE_MIN_WIDTH ? 'auto' : 'hidden',
    overflowY: applicationSize.height < TT.PAGE_MIN_HEIGHT ? 'auto' : 'hidden'
  });

  // Limit the screen size to the bounds
  applicationSize.width = Math.max(applicationSize.width, TT.PAGE_MIN_WIDTH);
  applicationSize.height = Math.max(applicationSize.height, TT.PAGE_MIN_HEIGHT);

  // Determine the center point of the application
  var center =
      { x: applicationSize.width * 0.5, y: applicationSize.height * 0.5 };

  // Only update component positioning if this update does not originate from a
  // scroll event.
  if (!fromScroll) {

    // When we reach a small browser window size we need to progressively hide
    // the grey mask that overlays the book.
    // TODO: Remove magical number 50.
    if (applicationSize.width < TT.PAGE_MIN_WIDTH + $('#grey-mask').width() +
        50) {
      $('#grey-mask').css({
        left: -((TT.PAGE_MIN_WIDTH + $('#grey-mask').width() + 50) -
            applicationSize.width)
      });
    }
    else {
      $('#grey-mask').css({
        left: 0
      });
    }

    // Align the book to the center of the page with the right side page in
    // focus.
    $('#book').css({
      left: center.x - (TT.BOOK_WIDTH * 0.5) - (TT.BOOK_WIDTH_CLOSED * 0.5) +
          TT.BOOK_OFFSET_X,
      top: center.y - (TT.BOOK_HEIGHT * 0.5),
      margin: 0
    });

    // Align the table of contents to the center of the screen
    $('#table-of-contents div.center').css({
      left: center.x -
          (parseInt($('#table-of-contents div.center').innerWidth()) * 0.5),
      top: center.y -
          (parseInt($('#table-of-contents div.center').height()) * 0.5),
      margin: 0
    });

    // Set explicit sizes to certain elements (100% width is not desirable due
    // to the min size logic).
    $('#table-of-contents, header, footer').css({
      width: applicationSize.width
    });

    // Align the credits to the center of the screen
    $('#credits').css({
      left: center.x - ($('#credits').width() * 0.5),
      top: center.y - ($('#credits').height() * 0.5),
      margin: 0
    });

    // Align the overlay bookmark to the center of the screen
    $('#overlay div.bookmark').css({
      left: center.x - ($('#overlay div.bookmark').width() * 0.5),
      top: center.y - ($('#overlay div.bookmark').height() * 0.5),
      margin: 0
    });

    // Align the footer to the bottom of the application
    $('footer').css({
      top: applicationSize.height - $('footer').height(),
      margin: 0
    });

    $('#search-dropdown').css({
      left: $('#search-field').position().left + 1,
      top: $('#search-field').position().top + $('#search-field').height() + 2
    });

    // Align the chapter nav to the bottom center of the book
    $('#chapter-nav').css({
      left: center.x - ($('#chapter-nav').width() * 0.5) + 5 + TT.BOOK_OFFSET_X,
      top: $('footer').position().top - $('#chapter-nav').outerHeight() + 5
    });
  }

  // Set the vertical positions of the pagination buttons to slightly above the
  // exact center point.
  $('#pagination-prev, #pagination-next').css({ top: center.y - 20 });

  // If we are not on a touch device, we need to account for scrolling when
  // positioning the pagination buttons horizontally.
  if (!TT.IS_TOUCH_DEVICE) {
    $('#pagination-prev').css({
      left: $(window).scrollLeft()
    });
    $('#pagination-next').css({
      right: 'auto',
      left: $(window).scrollLeft() + $(window).width() -
          $('#pagination-next').width()
    });
  }

};


/**
 * Outputs a log of the passed in object. This is centralized in one method so
 * that we can keep info logs around the site and easily disable/enable them
 * when jumping between live/dev.
 * @param {string} o Message to log to console.
 */
TT.log = function(o) {
  if (window.console && o) {
    window.console.log(o);
  }
};


/**
 * Sets up the Lights on/off functionality.
 */
TT.lights = (function() {

  $('footer div.lights a').click(function(e) {
    e.preventDefault();
    if ($('html').hasClass('dark')) {
      $(this).parent().removeClass('clone').appendTo('footer .lights-wrapper');
      setTimeout(function() { $('div.lights .icon').removeClass('off'); }, 0);
    } else {
      $(this).parent().addClass('clone').appendTo('body');
      setTimeout(function() { $('div.lights .icon').addClass('off'); }, 0);
    }
    $('html,body').toggleClass('dark');
  });

  $('.dark footer').live('hover',
      function() { $('div.lights').toggleClass('active') });

  return {
    turnOn: function() {
      $('html,body').removeClass('dark');
      $('div.lights').removeClass('clone').appendTo('footer .lights-wrapper');
      $('div.lights .icon').removeClass('off');
    }
  };

})();


/**
 * Updates layout to 'fullscreen off/on' mode.
 */
TT.fullscreen = (function() {

  function onEnterFullScreen() {
    $('div.fullscreen').addClass('clone').appendTo('body');
    $('div.fullscreen .icon').addClass('off');

    TT.pageflip.unregisterEventListeners();
    TT.updateLayout();

    TT.lights.turnOn();
  }

  function onExitFullScreen() {
    $('div.fullscreen').removeClass('clone').appendTo(
        'footer .fullscreen-wrapper');
    $('div.fullscreen .icon').removeClass('off');

    TT.pageflip.registerEventListeners();
    TT.updateLayout();
  }

  $('footer div.fullscreen a').click(function(e) {
    e.preventDefault();

    $('html,body').toggleClass('fullscreen');

    if ($('html').hasClass('fullscreen')) {
      onEnterFullScreen();
    }
    else {
      onExitFullScreen();
    }
  });

  $('footer .fullscreen-wrapper').show();

  return {
    exit: function() {
      $('html,body').removeClass('fullscreen');
      onExitFullScreen();
    }
  };
})();


/**
 * A global shorthand for retrieving the current time.
 * @return {Object} Date object.
 */
TT.time = function() {
  return new Date().getTime();
};


/**
 * Google Analytics tracking wrapper.
 * @param {string} url URL to track.
 */
TT.track = function(url) {
  _gaq.push(['_trackPageview', url]);
};


/**
 * Assign namespace to window object.
 */
window['TT'] = TT;
