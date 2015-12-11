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
 * @fileoverview The overlay class manages the showing and hiding of overlay
 * displays (resume reading and print) and also takes care of the button events
 * within those overlays.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.overlay = {};


/**
 * Overlay node.
 * @type {Object}
 */
TT.overlay.overlay = null;


/**
 * Bookmark node.
 * @type {Object}
 */
TT.overlay.bookmark = null;


/**
 * Print node.
 * @type {Object}
 */
TT.overlay.print = null;


/**
 * Flags if any overlay is currently being shown.
 * @type {boolean}
 */
TT.overlay.visible = false;


/**
 * Initialize overlay class.
 */
TT.overlay.initialize = function() {
  TT.overlay.overlay = $('#overlay');
  TT.overlay.bookmark = $('#overlay div.bookmark');
  TT.overlay.print = $('#overlay div.print');
};


/**
 * Shows the overlay with the bookmark that allows users to pick up where they
 * left off or start over.
 * @param {Object} continueCallback Callback for 'continue' response.
 * @param {Object} restartCallback Callback for 'restart' response.
 * @param {Object} cancelCallback Callback for 'cancel' response.
 */
TT.overlay.showBookmark = function(continueCallback, restartCallback,
    cancelCallback) {
  TT.overlay.overlay.stop().fadeIn(200);
  TT.overlay.bookmark.siblings().hide();
  TT.overlay.bookmark.stop().fadeIn(200);

  $('a.resume', TT.overlay.bookmark).click(function() {
    TT.overlay.hide();
    continueCallback();

    return false;
  });

  $('a.restart', TT.overlay.bookmark).click(function() {
    TT.overlay.hide();
    restartCallback();

    return false;
  });

  $('a.close', TT.overlay.bookmark).click(function() {
    TT.overlay.hide();
    cancelCallback();

    return false;
  });

  TT.overlay.visible = true;
  TT.overlay.hasShownBookmark = true;

  TT.pageflip.unregisterEventListeners();

  $('body').addClass('overlay');
};


/**
 * Shows the print dialog.
 */
TT.overlay.showPrint = function() {
  TT.overlay.overlay.stop().fadeIn('fast');
  TT.overlay.print.siblings().hide();
  TT.overlay.print.stop().fadeIn('fast');

  $('a.close', TT.overlay.print).click(function() {
    TT.overlay.hide();

    return false;
  });

  $('a.downloadPdf.disabled', TT.overlay.print).click(function() {
    return false;
  });

  TT.overlay.visible = true;

  TT.pageflip.unregisterEventListeners();

  $('body').addClass('overlay');
};


/**
 * Hides any currently open overlay window (print or bookmark).
 */
TT.overlay.hide = function() {
  TT.overlay.overlay.stop().fadeOut('fast');
  TT.overlay.bookmark.stop().fadeOut('fast');
  TT.overlay.print.stop().fadeOut('fast');

  TT.overlay.visible = false;

  TT.pageflip.registerEventListeners();

  $('body').removeClass('overlay');
};



