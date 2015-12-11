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
 * @fileoverview The navigation class ties the site together by making sure the
 * pagination and other in-site links are bound.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.navigation = {};


/**
 * Transitioning from hard cover flag.
 * @type {boolean}
 */
TT.navigation.transitioningFromHardCover = false;


/**
 * Flags if the user has carried out ANY page navigation.
 * @type {boolean}
 */
TT.navigation.hasNavigated = false;


/**
 * Current page name.
 * @type {string}
 */
TT.navigation.currentPageName = '';


/**
 * Queue navigation events.
 * @type {Object}
 */
TT.navigation.enqueueNavigation = null;


/**
 * Initialize navigation class.
 */
TT.navigation.initialize = function() {
  $('#pages section:not(.current)').width(0).hide();

  TT.navigation.assignNavigationHandlers();
  TT.navigation.assignNextPrevHandlers();

  // If we are starting up on the home page, activate the flip intro animation.
  if (TT.navigation.isHomePage()) {
    TT.flipintro.activate();
  }
  else {
    $('#front-cover').hide();
    $('#front-cover-bookmark').hide();
    $('#front-cover-arrow').hide();
  }

  // If we are not on the credits page, make sure the back cover is hidden.
  if (!TT.navigation.isCreditsPage()) {
    $('#back-cover').hide();
  }

  if (TT.navigation.isTableOfThings()) {
    $('body').addClass('home').addClass('tot');
  }
  // If we are not on the home page or credits page at start, we must be in the
  // book state.
  else if (!TT.navigation.isHomePage() && !TT.navigation.isCreditsPage()) {
    $('body').addClass('book');
  }

  // Update the sharer components state.
  TT.sharing.updateSharer();

  // Load images.
  TT.navigation.loadImages();

  // Check if we need to start any illustration animation.
  TT.illustrations.update($('#pages section.current'));

  TT.navigation.updatePageVisibility($('#pages section.current'));

};


/**
 * Assign navigation handlers.
 */
TT.navigation.assignNavigationHandlers = function() {

  // Bind the Logo button in the header.
  $('header a.logo').click(function() {
    TT.navigation.goToHome();
    return false;
  });

  // Bind the Foreword button in the header.
  $('header li.about a').click(function() {
    TT.navigation.goToPage(TT.history.FOREWORD, 1);
    return false;
  });

  // Bind the Credits button in the header.
  $('header li.credits a').click(function() {
    TT.navigation.goToCredits();
    return false;
  });

  // Bind the "Open Book" button in the home page bookmark.
  $('#front-cover-bookmark a.open-book').click(function() {
    TT.navigation.goToNextPage();
    return false;
  });

  // Bind the Table of Things button in the header.
  $('header li.table-of-things a').click(function() {
    TT.tableofthings.show();
    return false;
  });

  // Bind the Back button in the Table of Things view.
  $('#table-of-contents a.go-back').click(function() {
    TT.tableofthings.hide();
    return false;
  });

  // Bind the turn arrow on the front page.
  $('#front-cover-arrow').click(function() {
    TT.navigation.goToNextPage();
    return false;
  });

  // Bind the print overlay.
  $('footer .print a').click(function() {
    if (window.location.pathname.match('en-US')) {
      TT.overlay.showPrint();
      return false;
    }
  });

  // Bind events for local links within the book contents.
  $('#pages section a').click(function() {
    var link = $(this).attr('href');

    // Only "hijack" the click if it's a local link (to another page in the
    // book).
    if (link.indexOf('http://') == -1 && link.indexOf('www.') == -1) {
      var article = link.split('/')[1];
      var page = link.split('/')[2];

      if (article && page) {
        TT.navigation.goToPage(article, page);
      }

      return false;
    }
  });
};


/**
 * Assign event handlers to next/prev links.
 */
TT.navigation.assignNextPrevHandlers = function() {
  $('#pagination-prev').click(function(e) {
    e.preventDefault();
    TT.navigation.goToPreviousPage();
  });

  $('#pagination-next').click(function(e) {
    e.preventDefault();
    TT.navigation.goToNextPage();
  });

  var element = '<div class="page-progress">' +
      '<p class="thing"></p>' +
      '<p class="number">' + SERVER_VARIABLES.PAGE + ' <span></span></p>' +
      '</div>';

  if (!TT.IS_TOUCH_DEVICE) {
    $('#pagination-prev').append(element);
    $('#pagination-next').append(element);
  }
};


/**
 * Is home page.
 * @return {boolean} Whether is home page.
 */
TT.navigation.isHomePage = function() {
  return $('body').hasClass('home');
};


/**
 * Is credits page.
 * @return {boolean} Whether is credits page.
 */
TT.navigation.isCreditsPage = function() {
  return $('body').hasClass('credits');
};


/**
 * Is table of things.
 * @return {boolean} Whether is table of things.
 */
TT.navigation.isTableOfThings = function() {
  return $('body').hasClass('tot');
};


/**
 * Is book open.
 * @return {boolean} Whether book is open.
 */
TT.navigation.isBookOpen = function() {
  return $('body').hasClass('book');
};


/**
 * Is full screen.
 * @return {boolean} Whether is full screen.
 */
TT.navigation.isFullScreen = function() {
  return $('body').hasClass('fullscreen');
};


/**
 * Checks if we are on any page in the foreword chapter.
 * @param {Object=} target Current page.
 * @return {boolean} Whether is foreword.
 */
TT.navigation.isForeword = function(target) {
  if (!target) target = $('#pages section.current');
  return TT.navigation.classToArticle(target.attr('class')) ==
      TT.history.FOREWORD;
};


/**
 * Checks if we are on the last page before the book ends (the page before the
 * back cover).
 * @param {Object=} target Current page.
 * @return {boolean} Whether is last page.
 */
TT.navigation.isLastPage = function(target) {
  if (target) {
    return target.next('section').length == 0 && !TT.navigation.isCreditsPage();
  }
  return $('#pages section.current').next('section').length == 0 &&
      !TT.navigation.isCreditsPage();
};


/**
 * Checks if we are on the first page of the book.
 * @param {Object=} target Current page.
 * @return {boolean} Whether is first page.
 */
TT.navigation.isFirstPage = function(target) {
  if (target) {
    return target.prev('section').length == 0 && !TT.navigation.isHomePage();
  }
  return $('#pages section.current').prev('section').length == 0 &&
      !TT.navigation.isHomePage();
};


/**
 * Get article title from section className.
 * @param {string} theClass The className from a <section> element.
 * @return {?string} Article title.
 */
TT.navigation.classToArticle = function(theClass) {
  return theClass ? theClass.match(/title-([a-zA-Z-0-9]+)/)[1] : null;
};


/**
 * Get article page number from section className.e.
 * @param {string} theClass The className from a <section> element.
 * @return {?number} Article page number.
 */
TT.navigation.classToArticlePage = function(theClass) {
  return theClass ? parseInt(theClass.match(/page-([0-9]+)/)[1]) : null;
};


/**
 * Get global page number from section className.e.
 * @param {string} theClass The className from a <section> element.
 * @return {?number} Global page number.
 */
TT.navigation.classToGlobalPage = function(theClass) {
  return theClass ? parseInt(theClass.match(/globalPage-([0-9]+)/)[1]) : null;
};


/**
 * Manages 'inactive' class on next/prev links.
 * @param {Object} targetPage Target page node.
 */
TT.navigation.updateNextPrevLinks = function(targetPage) {

  if (TT.navigation.isCreditsPage()) {
    $('#pagination-next').addClass('inactive');
    $('#pagination-prev').removeClass('inactive');
  } else if (TT.navigation.isHomePage()) {
    $('#pagination-prev').addClass('inactive');
    $('#pagination-next').removeClass('inactive');
  } else {
    $('#pagination-prev, #pagination-next').removeClass('inactive');
  }

  var nextPage = TT.navigation.isHomePage() ? targetPage.attr('class') :
      targetPage.next('section').attr('class');
  var prevPage = targetPage.prev('section').attr('class');

  if (nextPage) {
    // Update the hint values for the next button
    TT.navigation.updatePaginationHint(nextPage, $('#pagination-next'));
  }
  else {
    $('#pagination-next .page-progress').hide();
  }

  if (prevPage && !TT.navigation.isLastPage() &&
      !TT.navigation.isCreditsPage()) {
    // Update the hint values for the previous button
    TT.navigation.updatePaginationHint(prevPage, $('#pagination-prev'));
  }
  else {
    $('#pagination-prev .page-progress').hide();
  }

};


/**
 * Updates the hint values such as the upcoming/previous chapter index and page
 * number in a pagination button.
 * @param {Object} page The page element from which values should be collected.
 * @param {Object} button The pagination button that will be updated with the
 *     hint values.
 */
TT.navigation.updatePaginationHint = function(page, button) {
  // Collect all of the required page numbers and indexes
  var articleId = TT.navigation.classToArticle(page);
  var articleIndex = $('#chapter-nav ul li').find('[data-article=' +
      articleId + ']').parent().index() + 1;
  var pageNumber = TT.navigation.classToArticlePage(page);
  var numberOfPages = $('#pages section.title-' + articleId).length;

  // If all required values are available, output them
  if (pageNumber != undefined && numberOfPages != undefined) {
    $('.page-progress', button).show();

    if (articleId == TT.history.FOREWORD) {
      $('p.thing', button).html(SERVER_VARIABLES.FOREWORD);
    }
    else {
      $('p.thing', button).html(SERVER_VARIABLES.THING + ' ' + articleIndex);
    }

    $('p.number span', button).text(pageNumber + '/' + numberOfPages);
  }
  // .. but in case any values are missing, don't show the progress at all
  else {
    $('.page-progress', button).hide();
  }
};


/**
 * Get current article ID.
 * @return {?string} Article ID.
 */
TT.navigation.getCurrentArticleId = function() {
  return TT.navigation.classToArticle($('#pages section.current')
      .attr('class'));
};


/**
 * Get current article page number.
 * @return {?number} Article page number.
 */
TT.navigation.getCurrentArticlePage = function() {
  return TT.navigation.classToArticlePage($('#pages section.current')
      .attr('class'));
};


/**
 * Load previous page if there is one.
 * @return {?boolean} False if conditions met.
 */
TT.navigation.goToPreviousPage = function() {

  // Clean up remnant transition flags
  TT.navigation.cleanUpTransitions();

  // Don't allow any transitions while a hard cover is being turned
  if (TT.navigation.transitioningFromHardCover) {
    return false;
  }

  if (TT.navigation.isFirstPage()) {
    // If we are on the first page of the book, navigate to the home view
    if (!TT.navigation.isHomePage()) {
      TT.pageflip.completeCurrentTurn();
      TT.navigation.goToHome();
    }
    return false;
  }

  TT.pageflip.completeCurrentTurn();

  var currentPage = $('#pages section.current');

  var prevArticle, prevPage = null;

  if (TT.navigation.isCreditsPage()) {
    prevArticle = TT.navigation.classToArticle(currentPage.attr('class'));
    prevPage = TT.navigation.classToArticlePage(currentPage.attr('class'));
  }
  else {
    prevArticle = TT.navigation.classToArticle(currentPage.prev('section')
        .attr('class'));
    prevPage = TT.navigation.classToArticlePage(currentPage.prev('section')
        .attr('class'));
  }

  TT.navigation.goToPage(prevArticle, prevPage);
};


/**
 * Load next page if there is one.
 * @return {?boolean} False if conditions met.
 */
TT.navigation.goToNextPage = function() {

  // Clean up remnant transition flags
  TT.navigation.cleanUpTransitions();

  // Don't allow any transitions while a hard cover is being turned
  if (TT.navigation.transitioningFromHardCover) {
    return false;
  }

  if (TT.navigation.isLastPage() || TT.navigation.isCreditsPage()) {
    // If we are on the last page of the book, navigate to the credits view
    if (!TT.navigation.isCreditsPage() || (TT.navigation.isCreditsPage() &&
        TT.navigation.isBookOpen())) {
      TT.pageflip.completeCurrentTurn();
      TT.navigation.goToCredits();
    }
    return false;
  }

  TT.pageflip.completeCurrentTurn();

  var currentPage = $('#pages section.current');

  var prevArticle, prevPage = null;

  if (TT.navigation.isHomePage()) {
    nextArticle = TT.navigation.classToArticle(currentPage.attr('class'));
    nextPage = TT.navigation.classToArticlePage(currentPage.attr('class'));
  }
  else {
    TT.pageflip.completeCurrentTurn();
    nextArticle = TT.navigation.classToArticle(currentPage.next('section')
        .attr('class'));
    nextPage = TT.navigation.classToArticlePage(currentPage.next('section')
        .attr('class'));
  }

  TT.navigation.goToPage(nextArticle, nextPage);
};


/**
 * Navigates to the home page through a hard flip transition.
 * @param {boolean} fromHistoryChange Whether from history change.
 */
TT.navigation.goToHome = function(fromHistoryChange) {

  TT.tableofthings.hide();

  if (!TT.navigation.isHomePage()) {

    if (TT.navigation.isCreditsPage()) {
      TT.navigation.enqueueNavigation = {
        call: function() {
          // This callback should only be triggered once
          delete this.call;

          // Timeout used to exit cycle
          setTimeout(TT.navigation.goToHome, 1);
        }
      };

      TT.navigation.goToPage(TT.history.THEEND, 1, false);

      return;
    }

    // Update the current page name
    TT.navigation.currentPageName = TT.history.HOME;

    $('#back-cover').hide();
    //$("#right-page").width(TT.BOOK_WIDTH_CLOSED).show();

    // Add the view specific body class
    $('body').removeClass('book').removeClass(TT.history.CREDITS)
        .addClass(TT.history.HOME);

    // Activate the flip introduction animation on the home page
    TT.flipintro.activate();

    // Update the sharer components state
    TT.sharing.updateSharer();

    // Flag that we are not transition away from a hardcover
    TT.navigation.transitioningFromHardCover = false;

    // Make sure that the first page is marked as current (in case the header
    // nav buttons are used).
    $('#pages section').removeClass('current');
    $('#pages section').first().addClass('current');

    // The currently visible page, i.e. the page we are leaving
    var currentPage = $('#pages section.current');
    currentPage.width(TT.PAGE_WIDTH);

    //TT.navigation.updatePageVisibility(currentPage, -1);

    if (!fromHistoryChange) {
      // Push the current URL to the history
      TT.history.pushState('/' + TT.locale.getLocaleCodeFromURL() + '/home');
    }

    // Execute the transition to the home page
    TT.pageflip.turnToPage(currentPage, currentPage, -1, TT.pageflip.HARD_FLIP);
  }
};


/**
 * Navigates to the credits page through a hard flip transition.
 * @param {boolean} fromHistoryChange Whether from history change.
 */
TT.navigation.goToCredits = function(fromHistoryChange) {

  TT.tableofthings.hide();

  if (!TT.navigation.isCreditsPage() || (TT.navigation.isCreditsPage() &&
      TT.navigation.isBookOpen())) {

    if ((TT.navigation.isBookOpen() || TT.navigation.isHomePage()) &&
        (!TT.navigation.isLastPage() && !TT.navigation.isCreditsPage())) {
      TT.navigation.enqueueNavigation = {
        call: function() {
          // This callback should only be triggered once
          delete this.call;

          // Timeout used to exit cycle
          setTimeout(TT.navigation.goToCredits, 1);
        }
      };

      TT.navigation.goToPage(TT.history.THEEND, 1, false);
      TT.paperstack.updateStack(1);

      return;
    }

    // Update the current page name
    TT.navigation.currentPageName = TT.history.CREDITS;

    $('#page-shadow-overlay').hide();
    $('#front-cover').hide();
    $('#front-cover-bookmark').hide();
    $('#front-cover-arrow').hide();

    // Add the view specific body class
    $('body').removeClass('book').removeClass(TT.history.HOME)
        .addClass(TT.history.CREDITS);

    // Update the sharer components state
    TT.sharing.updateSharer();

    // Flag that we are not transition away from a hardcover
    TT.navigation.transitioningFromHardCover = false;

    // Make sure that the last page is marked as current (in case the header
    // nav buttons are used).
    $('#pages section').removeClass('current');
    $('#pages section').last().addClass('current');

    // The currently visible page, i.e. the page we are leaving
    var currentPage = $('#pages section.current');

    TT.navigation.updatePageVisibility(currentPage, 1);

    if (!fromHistoryChange) {
      // Push the current URL to the history
      TT.history.pushState('/' + TT.locale.getLocaleCodeFromURL() + '/credits');
    }

    // Execute the transition to the credits page
    TT.pageflip.turnToPage(currentPage, currentPage, 1, TT.pageflip.HARD_FLIP);
  }
};


/**
 * Navigates to a specific page in the book.
 * @param {string} articleId The article ID (ie. 'cloud-computing').
 * @param {number} pageNumber The page number (relative to the article) that
 *     should be navigated to.
 * @param {boolean} fromHistoryChange If flagged as true, there will be no
 *     transition.
 * @return {boolean} True if successful.
 */
TT.navigation.goToPage = function(articleId, pageNumber, fromHistoryChange) {

  TT.navigation.loadImages(articleId, pageNumber);

  if (TT.navigation.isCreditsPage() && articleId !== TT.history.THEEND) {

    TT.navigation.enqueueNavigation = {
      articleId: articleId,
      pageNumber: pageNumber,
      fromHistoryChange: fromHistoryChange,

      call: function() {
        // This callback should only be triggered once
        delete this.call;

        TT.navigation.goToPage(this.articleId, this.pageNumber,
            this.fromHistoryChange);
      }
    };

    // Force a navigation to the end page before continuing to credits
    articleId = TT.history.THEEND;
    pageNumber = 1;

  }

  // The currently visible page, i.e. the page we are leaving
  var currentPage = $('#pages section.current');

  // The page that we are navigating too, this page will be the new
  // "currentPage".
  var targetPage = $('#pages section.title-' + articleId + '.page-' +
      pageNumber);

  TT.navigation.hasNavigated = true;

  TT.tableofthings.hide();

  // We should never navigate to the page we are already on.
  var isSamePageInBook = currentPage.attr('class') === targetPage.attr('class');
  var isSamePageOverall = targetPage.attr('class') ===
      TT.navigation.currentPageName;

  if ((!isSamePageOverall && !isSamePageInBook) ||
      (TT.navigation.isHomePage() || TT.navigation.isCreditsPage())) {

    TT.navigation.currentPageName = targetPage.attr('class');

    if (TT.navigation.classToArticle(TT.navigation.currentPageName) ==
        TT.history.THEEND) {
      TT.sharing.updateSharer(true);
    }

    // Assume that we will be doing a soft flip
    var type = TT.pageflip.SOFT_FLIP;

    // If we are on either the home or credits pages, change the transition to
    // hard cover.
    if (TT.navigation.isHomePage() || TT.navigation.isCreditsPage()) {
      type = TT.pageflip.HARD_FLIP;

      TT.navigation.transitioningFromHardCover = true;
    }

    // Determine the global page numbers of the current and target pages
    var currentGlobalPageNumber = TT.navigation.classToGlobalPage($('.current')
        .attr('class'));
    var targetGlobalPageNumber = TT.navigation.classToGlobalPage(targetPage
        .attr('class'));

    if (currentGlobalPageNumber != null && targetGlobalPageNumber != null) {
      // Determine how many pages we are stepping past
      var steps = Math.abs(currentGlobalPageNumber - targetGlobalPageNumber);

      // Using the global page numbers, we can determine which direction we are
      // navigating in.
      var direction = targetGlobalPageNumber > currentGlobalPageNumber ? 1 : -1;

      // Special case for the home and credits pages which don't have page
      // numbers and directions.
      if (targetGlobalPageNumber == currentGlobalPageNumber) {
        direction = TT.navigation.isHomePage() ? 1 : -1;
      }

      TT.navigation.updatePageVisibility(targetPage, direction, steps);

      // Execute the transition from the current to the target page
      TT.pageflip.turnToPage(currentPage, targetPage, direction, type);

      if (!fromHistoryChange) {
        // Push the current URL to the history
        TT.history.pushState('/' + TT.locale.getLocaleCodeFromURL() + '/' +
            articleId + '/' + pageNumber);
      }

      // Make sure the bookmark is up to date with the latest navigation
      TT.storage.setBookmark(articleId, pageNumber);

      TT.navigation.updateNextPrevLinks(targetPage);

      TT.navigation.updatePageReferences(articleId);

      return true;
    }
  }

  return false;
};


/**
 * Hides uninvolved sections, shows target section and prepares previous width.
 * @param {Object} targetPage Target page.
 * @param {number} direction Direction of page movement.
 * @param {number} steps Number of steps/pages being moved.
 */
TT.navigation.updatePageVisibility = function(targetPage, direction, steps) {
  steps = steps || 0;

  // Store the depth of the current page
  var currentDepth = parseInt($('#pages section.current').css('z-index'));

  // If we are jumping multiple steps or are on the home page, then use compare
  // with the depth of the page we are going to.
  if (steps > 1 || TT.navigation.isHomePage()) {
    currentDepth = parseInt(targetPage.css('z-index'));
  }

  // All pages that are at a higher depth than the current page need to be set
  // to zero width.
  $('#pages section:not(.current)').each(function() {
    var z = parseInt($(this).css('z-index'));

    if (z > currentDepth) {
      $(this).width(0).hide().css('top');
    }
    // Hide all pages further ahead in the book, improves performance since the
    // browser does not need to render all pages all of the time.
    else if (z < currentDepth - 1) {
      $(this).hide();
    }
  });

  // Show the page we are navigating too
  targetPage.show();

  // Special case, if we are navigating from the home page multiple steps into
  // the book, make sure the current (first) page is hidden.
  if (steps > 1 && direction == 1 && TT.navigation.isHomePage()) {
    $('#pages section.current').width(0).hide();
    targetPage.width(TT.PAGE_WIDTH).show();
  }

  if (!TT.navigation.isHomePage()) {
    $('#left-page').width(TT.BOOK_WIDTH_CLOSED).show();
  }
};


/**
 * Update current pointer.
 * @param {Object} currentPage Current page.
 * @param {Object} targetPage Target page.
 */
TT.navigation.updateCurrentPointer = function(currentPage, targetPage) {

  if (TT.navigation.transitioningFromHardCover) {
    $('body').removeClass(TT.history.HOME).removeClass(TT.history.CREDITS)
        .addClass('book');

    $('#page-shadow-overlay').hide();

    TT.navigation.transitioningFromHardCover = false;
  }

  currentPage.removeClass('current');
  targetPage.addClass('current');

  TT.sharing.updateSharer();
  TT.navigation.updatePageReferences();
  TT.navigation.updateNextPrevLinks(targetPage);

  if (TT.navigation.enqueueNavigation && TT.navigation.enqueueNavigation.call) {
    TT.navigation.enqueueNavigation.call();
    TT.navigation.enqueueNavigation = null;
  }
};


/**
 * Update page references.
 * @param {string} articleId Article ID.
 */
TT.navigation.updatePageReferences = function(articleId) {
  TT.chapternav.updateSelection(articleId);
  TT.tableofthings.updateSelection(articleId);
  TT.paperstack.updateStack();

  TT.illustrations.update($('#pages section.current'));
};


/**
 * Clean up transitions.
 * @param {Object} currentPage Current page.
 * @param {Object} targetPage Target page.
 */
TT.navigation.cleanUpTransitions = function(currentPage, targetPage) {

  TT.pageflip.removeInactiveFlips();

  if (TT.pageflip.flips.length == 0) {
    TT.navigation.transitioningFromHardCover = false;
  }

};


/**
 * Load images for current page, preload for subsequent pages.
 * @param {string} articleId Article ID.
 * @param {pageNumber} pageNumber Page number.
 */
TT.navigation.loadImages = function(articleId, pageNumber) {
  var cur = articleId && pageNumber ? $('#pages section.title-' + articleId +
      '.page-' + pageNumber) : $('#pages section.current');
  var pages = [cur];
  if (cur.prev('section').length) pages.push(cur.prev('section'));
  if (cur.next('section').length) pages.push(cur.next('section'));

  for (var i = 0; i < pages.length; i++) {
    pages[i].find('img').each(function() {
      if ($(this).attr('src') !== $(this).attr('data-src')) {
        $(this).attr('src', $(this).attr('data-src'));
      }
    });
  }

};
