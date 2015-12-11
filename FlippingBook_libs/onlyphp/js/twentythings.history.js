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
 * @fileoverview Manages the application history for browser navigations
 * such as forward/backwards. Two different approaches are used for this:
 * 1) Modern browsers with support for HTML5's History API
 *    will use non-hash URL's such as www.example.com/chapter/page.
 * 2) Browser that do NOT support the History API will fall
 *    back to using hash URL's such as www.example.com/#/chapter/page.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.history = {};


/**
 * Table of things URL stub.
 * @type {string}
 */
TT.history.TABLE_OF_CONTENTS = 'table-of-things';


/**
 * Home URL stub.
 * @type {string}
 */
TT.history.HOME = 'home';


/**
 * Foreword URL stub.
 * @type {string}
 */
TT.history.FOREWORD = 'foreword';


/**
 * The end URL stub.
 * @type {string}
 */
TT.history.THEEND = 'theend';


/**
 * Credits URL stub.
 * @type {string}
 */
TT.history.CREDITS = 'credits';


/**
 * Previous URL stub.
 * @type {string}
 */
TT.history.previousHash = '';


/**
 * An interval used to check if the hash has changed.
 * @type {number}
 */
TT.history.hashCheckInterval = -1;


/**
 * Determine history capabilities and initiate approrpriate monitoring.
 */
TT.history.initialize = function() {

  // Set up monetoring of changes to the history depending on which type of
  // history the browser supports.
  if (TT.history.supportsHistoryPushState()) {
    // Woop, History API support detected. All we need now
    // is to listen to the popstate event.
    $(window).bind('popstate', TT.history.onPopState);

  } else {

    // Bummer, we need to fall back to hackistry and manually
    // monitor changes to the URL.
    TT.history.hashCheckInterval = setInterval(TT.history.onCheckHash, 200);

    // A third alternative, would be to use the onhashchange
    // event for browers that support it - but for the sake
    // of reducing fragmentation in our code, let's not.
  }
};


/**
 * Check if HTML5's History API is supported.
 * @return {boolean} Whether HTML5 history is supportd.
 */
TT.history.supportsHistoryPushState = function() {
  return ('pushState' in window.history) && window.history['pushState'] !==
      null;
};


/**
 * Called at an interval for browsers that do not support the history API.
 * Checks if the hash has changed and issues a navigation if so.
 */
TT.history.onCheckHash = function() {
  if (document.location.hash !== TT.history.previousHash) {
    TT.history.navigateToPath(document.location.hash.slice(1));
    TT.history.previousHash = document.location.hash;
  }
};


/**
 * Pushes a URL to the history stack, effectively causing that URL to become
 * the current location.
 * @param {string} url The URL that should be pushed to the history stack.
 */
TT.history.pushState = function(url) {
  if (TT.history.supportsHistoryPushState()) {
    window.history.pushState('', '', url);
  }
  else {
    TT.history.previousHash = '#' + url;
    document.location.hash = url;
  }

  // Google Analytics tracking
  TT.track(url);
};


/**
 * Event handler for the window.onpopstate event. Causes the application to
 * navigate to the state that was popped.
 * @param {Object} event Pop state event object.
 */
TT.history.onPopState = function(event) {
  TT.history.navigateToPath(document.location.pathname);
};


/**
 * Navigates to a certain path in the application. The application does not use
 * paths to navigate internally so we need this intermediary function to
 * translate the path (i.e. URL) into an actual navigation.
 * @param {string} pathname The deeplink path which we want to navigate to,
 *     such as "/chapter/3" or "/home".
 */
TT.history.navigateToPath = function(pathname) {
  pathname = TT.locale.removeLocaleCodeFromURL(pathname);

  // Extract the path name parts.
  var part1 = pathname.split('/')[1]; // The article ID or home/credits.
  var part2 = pathname.split('/')[2]; // The page number.

  // If part one is home or invalid, go to the home page.
  if (!part1 || part1 == TT.history.HOME) {
    TT.navigation.goToHome(true);
  }
  // Check for the credits page explicitly.
  else if (part1 == TT.history.CREDITS) {
    TT.navigation.goToCredits(true);
  }
  // Check for the table of contents page explicitly.
  else if (part1 == TT.history.TABLE_OF_CONTENTS) {
    TT.tableofthings.show();
  }
  // If none of the above was true, we must be navigating to a a chapter
  // within the book.
  else {
    if (part2) {
      // Handle a two level deep path (for example "/chapter/3").
      TT.navigation.goToPage(part1, part2, true);
    }
    else {
      // Handle a one level deep path (for example "/chapter/").
      TT.navigation.goToPage(part1, '1', true);
    }
  }
};
