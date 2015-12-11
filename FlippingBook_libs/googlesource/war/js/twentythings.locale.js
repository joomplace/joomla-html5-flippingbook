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
 * @fileoverview Localization tools: language selector and URL parsing.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.locale = {};


/**
 * Locale title.
 * @type {Object}
 */
TT.locale.title = null;


/**
 * Locale list element.
 * @type {Object}
 */
TT.locale.list = null;


/**
 * Initialize locale tools.
 * @this {Object} Class object.
 */
TT.locale.initialize = function() {
  this.title = $('#language-selector-title');
  this.list = $('#language-selector-list');

  this.title.click(function() {
    if ($(this).hasClass('open')) {
      TT.locale.closeList();
    }
    else {
      TT.locale.openList();
    }
  });

  // Take over the language drop down links so that we can control which chapter
  // is opened when changing languages.
  $('li a', this.list).attr('href', '#').click(function(event) {
    var targetLocale = $(this).parents('li').attr('data-locale');
    var targetURL =
        TT.locale.removeLocaleCodeFromURL(document.location.pathname);

    document.location = '/' + targetLocale + targetURL;

    event.preventDefault();
  });

  // Prevent text selection of the title.
  this.title.mousedown(function(event) {
    event.preventDefault();
  });
};


/**
 * On mouse down handler.
 * @param {Object} event Mouse event object.
 */
TT.locale.onDocumentMouseDown = function(event) {
  if ($(event.target).parents('#language-selector').length === 0) {
    // The element that was clicked is NOT a child of the
    // language selector so we should close the list.
    TT.locale.closeList();
  }
};


/**
 * Open language list.
 * @this {Object} Class object.
 */
TT.locale.openList = function() {
  this.title.addClass('open');
  this.list.addClass('open');

  $(document).bind('mousedown', this.onDocumentMouseDown);
};


/**
 * Close language list.
 * @this {Object} Class object.
 */
TT.locale.closeList = function() {
  this.title.removeClass('open');
  this.list.removeClass('open');

  $(document).unbind('mousedown', this.onDocumentMouseDown);
};


/**
 * Get locale code from URL.
 * @return {string} Locale code string.
 */
TT.locale.getLocaleCodeFromURL = function() {
  var code = document.location.pathname;

  if (code.indexOf('fil-PH') > 0) {
    code = code.match(/\/fil-PH/gi) || '';
  } else if (code.indexOf('es-419') > 0) {
    code = code.match(/\/es-419/gi) || '';
  } else {
    code = code.match(/\/(..\-..)/gi) || '';
  }

  code = code.toString().replace(/\//gi, '');

  if (!code) {
    return 'en-US';
  }

  return code;
};


/**
 * Get language from locale code.
 * @param {string} localeCode Locale code.
 * @return {string} Language string.
 */
TT.locale.getLanguageFromLocaleCode = function(localeCode) {
  var languageCode = localeCode.slice(0, localeCode.indexOf('-'));

  if (!languageCode) {
    return locale;
  }

  return languageCode;
};


/**
 * Remove locale code from URL.
 * @param {string} url URL.
 * @return {string} URL without locale.
 */
TT.locale.removeLocaleCodeFromURL = function(url) {
  if (url.indexOf('fil-PH') > 0) {
    return url.replace(/\/fil-PH/gi, '');
  } else if (url.indexOf('es-419') > 0) {
    return url.replace(/\/es-419/gi, '');
  } else {
    return url.replace(/\/(..\-..)/gi, '');
  }
};
