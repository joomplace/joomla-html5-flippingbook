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
 * @fileoverview Handles the social sharing functionality in the book. Buttons
 * for sharing are available in the footer, on the back cover as well as on the
 * red bookmark inside of the book.
 *
 * Two types of sharing is supported, you can...
 * 1) Share an individual page, such as www.example.com/chapter/3
 * 2) Share the entire book, this means that users who click the linkd that you
 *    shared will arrive at the home page.
 *
 * Sharing is supported for three different social platforms: Facebook, Twitter
 * and Buzz.
 *
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.sharing = {};


/**
 * Current location.
 */
var l = window.location;


/**
 * Full location string.
 */
TT.sharing.BASE_URL = l.protocol + '//' + l.hostname + '/' +
    SERVER_VARIABLES.LANG;


/**
 * Facebook share link.
 */
TT.sharing.FACEBOOK_SHARER = 'http://www.facebook.com/sharer.php';


/**
 * Twitter share link.
 */
TT.sharing.TWITTER_SHARER = 'http://twitter.com/share';


/**
 * Buzz share link.
 */
TT.sharing.BUZZ_SHARER = 'http://www.google.com/buzz/post';


/**
 * Initialize sharing class.
 */
TT.sharing.initialize = function() {
  $('footer div.sharing .facebook, #credits div.share .facebook')
      .click(TT.sharing.shareBookOnFacebook);
  $('footer div.sharing .twitter, #credits div.share .twitter')
      .click(TT.sharing.shareBookOnTwitter);
  $('footer div.sharing .buzz, #credits div.share .buzz')
      .click(TT.sharing.shareBookOnBuzz);
  $('footer div.sharing .url').click(TT.sharing.openClipboardNotification);

  $('#sharer div.content ul li.facebook')
      .click(TT.sharing.shareChapterOnFacebook);
  $('#sharer div.content ul li.twitter')
      .click(TT.sharing.shareChapterOnTwitter);
  $('#sharer div.content ul li.buzz').click(TT.sharing.shareChapterOnBuzz);
  $('#sharer div.content ul li.print').click(TT.sharing.printThing);

  $(document).mousedown(TT.sharing.documentMouseDownHandler);
};


/**
 * Updates the visibility of the sharer component depending on what view we are\
 * in. The share should only be visible inside of the book.
 * @param {boolean} hide Whether to hide.
 */
TT.sharing.updateSharer = function(hide) {

  var articleId = TT.navigation.classToArticle($('#pages section.current')
      .attr('class'));

  $('#sharer div.content ul li.print a').attr('href', '/' +
      SERVER_VARIABLES.LANG + '/' + articleId + '/print');

  if (TT.navigation.isHomePage() || TT.navigation.isCreditsPage() ||
      TT.navigation.isLastPage() || TT.navigation.isForeword() || hide) {
    $('#sharer').stop(true, true);
    $('#sharer').fadeOut(150);
  }
  else {
    $('#sharer').stop(true, true).delay(150).fadeIn(150);
  }
};


/**
 * Updates the index which is displayed in the sharer component.
 * @param {number} index Sharer index.
 */
TT.sharing.updateSharerIndex = function(index) {
  if (index != 0) {

    // Don't update the index if this number is already written in the DOM.
    if (index != $('#sharer div.content p.index span').text()) {

      // If we are moving ahead quickly, there is a chance of multiple spans
      // stacking up - limit the number of simultaneous spans to two.
      $('#sharer div.content p.index span').each(function(i) {
        if (i > 1) {
          $(this).remove();
        }
      });

      // Fade out and remove the previous number.
      $('#sharer div.content p.index span').delay(300).fadeOut(200, function() {
        $(this).remove();
      });

      // Construct a new span with the current index.
      var span = $('<span>' + index + '</span>');

      // Create a show animation for the span.
      span.hide().delay(300).fadeIn(200);

      // Append the span to the "Thing #" paragraph.
      $('#sharer div.content p.index').append(span);

      // Ensure that the visibility of the sharer component is up to date.
      TT.sharing.updateSharer();
    }
  }
  else {
    $('#sharer').fadeOut();
  }
};


/**
 * Shows the copy-to-clipboard component and selects the URL therein so that
 * the user can immediately copy.
 * @return {boolean} Return false.
 */
TT.sharing.openClipboardNotification = function() {
  $('footer .clipboard-notification').show().focus().select();

  return false;
};


/**
 * If the user clicks anywhere outside of the copy-to-clipboard component, it
 * will be hidden.
 * @param {Object} event Event object.
 * @return {boolean} Return false.
 */
TT.sharing.documentMouseDownHandler = function(event) {
  if (event && event.target === $('footer .clipboard-notification')[0]) {
    $('footer .clipboard-notification').focus().select();
    return false;
  }
  else {
    $('footer .clipboard-notification').fadeOut(200);
  }
};


/**
 * Shares the full book on Facebook.
 * @return {boolean} Return false.
 */
TT.sharing.shareBookOnFacebook = function() {
  var url = TT.sharing.BASE_URL;
  var title = SERVER_VARIABLES['FACEBOOK_MESSAGE'];

  TT.sharing.shareOnFacebook(url, title);

  return false;
};


/**
 * Shares the full book on Twitter.
 * @return {boolean} Return false.
 */
TT.sharing.shareBookOnTwitter = function() {
  var url = TT.sharing.BASE_URL;
  var title = SERVER_VARIABLES['TWITTER_MESSAGE'];

  TT.sharing.shareOnTwitter(url, title);

  return false;
};


/**
 * Shares the full book on Buzz.
 * @return {boolean} Return false.
 */
TT.sharing.shareBookOnBuzz = function() {
  var url = TT.sharing.BASE_URL;
  var title = SERVER_VARIABLES['BUZZ_MESSAGE'];

  TT.sharing.shareOnBuzz(url, title);

  return false;
};


/**
 * Shares the current chapter on Facebook.
 * @return {boolean} Return false.
 */
TT.sharing.shareChapterOnFacebook = function() {
  var url = TT.sharing.BASE_URL + '/' + TT.navigation.getCurrentArticleId();
  var title = SERVER_VARIABLES['FACEBOOK_MESSAGE_SINGLE'];

  TT.sharing.shareOnFacebook(url, title);

  return false;
};


/**
 * Shares the current chapter on Twitter.
 * @return {boolean} Return false.
 */
TT.sharing.shareChapterOnTwitter = function() {
  var url = TT.sharing.BASE_URL + '/' + TT.navigation.getCurrentArticleId();
  var title = SERVER_VARIABLES['TWITTER_MESSAGE_SINGLE'];

  TT.sharing.shareOnTwitter(url, title);

  return false;
};


/**
 * Shares the current chapter on Buzz.
 * @return {boolean} Return false.
 */
TT.sharing.shareChapterOnBuzz = function() {
  var url = TT.sharing.BASE_URL + '/' + TT.navigation.getCurrentArticleId();
  var title = SERVER_VARIABLES['BUZZ_MESSAGE_SINGLE'];

  TT.sharing.shareOnBuzz(url, title);

  return false;
};


/**
 * Share on Facebook.
 * @param {string} url URL.
 * @param {string} title Title.
 */
TT.sharing.shareOnFacebook = function(url, title) {
  var shareURL = TT.sharing.FACEBOOK_SHARER;
  shareURL += '?u=' + encodeURIComponent(url);
  shareURL += '&t=' + encodeURIComponent(title);

  window.open(shareURL, 'Facebook',
      'toolbar=0,status=0,width=726,location=no,menubar=no,height=436');
};


/**
 * Share on Twitter.
 * @param {string} url URL.
 * @param {string} title Title.
 */
TT.sharing.shareOnTwitter = function(url, title) {
  var shareURL = TT.sharing.TWITTER_SHARER;
  shareURL += '?original_referer=' + encodeURIComponent(url);
  shareURL += '&text=' + encodeURIComponent(title);
  shareURL += '&url=' + encodeURIComponent(url);

  window.open(shareURL, 'Twitter',
      'toolbar=0,status=0,width=726,location=no,menubar=no,height=436');
};


/**
 * Share on Buzz.
 * @param {string} url URL.
 * @param {string} message Message.
 */
TT.sharing.shareOnBuzz = function(url, message) {
  var shareURL = TT.sharing.BUZZ_SHARER;
  shareURL += '?url=' + encodeURIComponent(url);
  shareURL += '&message=' + encodeURIComponent(message);
  shareURL += '&imageurl=' + encodeURIComponent(TT.sharing.BASE_URL +
      '/css/images/front-cover.jpg');

  window.open(shareURL, 'Buzz',
      'toolbar=0,status=0,width=726,location=no,menubar=no,height=436');
};
