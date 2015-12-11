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
 * @fileoverview Manages the search functionality which enables users to
 * instantly search through the content of the book using an input field in the
 * top right corner of the app. Since all of the book contents is downloaded
 * immediately as the application starts, the search logic can be (and is)
 * handled entirely on the client-side. This makes for a very responsive
 * experience to the end user.
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.search = {};


/**
 * The maximum number of matched CHAPTERS to show.
 */
TT.search.THING_RESULTS_LIMIT = 2;


/**
 * The maximum number of matched PARAGRAPHS to show.
 */
TT.search.KEYWORD_RESULTS_LIMIT = 8;


/**
 * The default (placeholder) text for the search box. (We're not using
 * type=search for this input due to styling inconsistencies between browsers).
 */
TT.search.DEFAULT_TEXT = $('#search-field').attr('value');


/**
 * Search dropdown.
 */
TT.search.dropdown = null;


/**
 * Search results.
 */
TT.search.dropdownResults = null;


/**
 * Search dropdown titles.
 */
TT.search.dropdownTitles = null;


/**
 * Search dropdown keywords.
 */
TT.search.dropdownKeywords = null;


/**
 * Search field.
 */
TT.search.field = null;


/**
 * Whether search has focus.
 */
TT.search.hasFocus = false;


/**
 * Search title results.
 */
TT.search.titleResults = [];


/**
 * Search keyword results.
 */
TT.search.keywordResults = [];


/**
 * Search hide interval.
 */
TT.search.hideInterval = -1;


/**
 * Initialize search class.
 */
TT.search.initialize = function() {

  // Fetch references to the HTML elements related to search.
  TT.search.field = $('#search-field');
  TT.search.dropdown = $('#search-dropdown');
  TT.search.dropdownResults = $('#search-dropdown div.results');
  TT.search.dropdownTitles = $('#search-dropdown div.results .things');
  TT.search.dropdownKeywords = $('#search-dropdown div.results .keywords');

  TT.search.field.focus(TT.search.onSearchFieldFocus);
  TT.search.field.blur(TT.search.onSearchFieldBlur);
  TT.search.field.change(TT.search.onSearchFieldChange);
  TT.search.field.keyup(TT.search.onSearchFieldChange);

  TT.search.field.click(function(event) {
    if (TT.search.field.val() == '') {
      TT.search.onSearchFieldChange(event);
    }
  });
};


/**
 * On search field focus.
 * @param {Object} event Event object.
 */
TT.search.onSearchFieldFocus = function(event) {
  clearInterval(TT.search.hideInterval);

  if (event.target.value === TT.search.DEFAULT_TEXT) {
    event.target.value = '';
  }

  TT.search.showResult();

  TT.search.hasFocus = true;

  $('header, #search-dropdown').addClass('searching');
};


/**
 * On search field blur.
 * @param {Object} event Event object.
 */
TT.search.onSearchFieldBlur = function(event) {
  clearInterval(TT.search.hideInterval);

  if (event.target.value === '') {
    event.target.value = TT.search.DEFAULT_TEXT;
  }

  // Hiding the search results needs to be delayed so that any possible click on
  // a result goes throguh before the dropdown hides
  TT.search.hideInterval = setInterval(TT.search.hideResults, 100);

  TT.search.hasFocus = false;

  $('header, #search-dropdown').removeClass('searching');
};


/**
 * On search field change.
 * @param {Object} event Event object.
 */
TT.search.onSearchFieldChange = function(event) {
  clearInterval(TT.search.hideInterval);

  if (TT.search.field.val() == '' || TT.search.field.val().length < 2) {
    TT.search.titleResults = [];
    TT.search.keywordResults = [];
    TT.search.hideResults();
  }
  else {
    TT.search.searchFor(TT.search.field.val());
  }

};


/**
 * Search for a term.
 * @param {string} term Search term.
 */
TT.search.searchFor = function(term) {

  // Clear results array.
  TT.search.titleResults = [];
  TT.search.keywordResults = [];

  // Escape special characters.
  TT.search.regexEscape = function(text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
  }

  // Create regex obj with field val.
  var searchPattern = new RegExp(TT.search.regexEscape(term), 'gi');

  // Loop through 'indexed' elements.
  $('.page h2, .page h3, .page p').each(function() {
    var elBeingSearched = $(this);
    var elText = $(this).text();

    // Test each element's text against pattern.
    if (searchPattern.test(elText)) {

      // Get array of varied case matches.
      var matchVariations = elText.match(searchPattern);

      // Create new object with only unique keys of case matches.
      var uniqueMatchVariations = {};

      for (var i = 0; i < matchVariations.length; i++) {
        uniqueMatchVariations[matchVariations[i]] = true;
      }

      // For each unique case match, create single result object and add to
      // results array.
      for (term in uniqueMatchVariations) {
        var elResults = elText.split(term);
        for (i = 1; i < elResults.length; i++) {
          var result = {};
          var anteSnippet = elResults[i - 1].substr(-10).replace(/</, '&lt;');
          var postSnippet = elResults[i].substr(0, 10).replace(/</, '&lt;');

          result.articleId = elBeingSearched.parents('section').eq(0)
              .attr('class').match(/title-([a-z-0-9]+)/)[1];
          term = term.replace(/</, '&lt;');
          result.snippet = anteSnippet + '<strong>' + term + '</strong>' +
              postSnippet;

          var chapterElement = $('#chapter-nav ul li').find('[data-article=' +
              result.articleId + ']');

          if (chapterElement.length > 0) {

            // Collect all data that's required to display this search result.
            result.articlePage = elBeingSearched.parents('section').eq(0)
                .attr('class').match(/page-([0-9]+)/)[1];
            result.articleIndex = chapterElement.parent().index() + 1;
            result.articleTitle = chapterElement.attr('data-title');
            result.articleGlobalStartPage = $('.pageNumber',
                elBeingSearched.parents('section')).text();
            result.articleGlobalEndPage =
                chapterElement.attr('data-globalendpage');

            // Truncate the title in case its too long.
            if (result.articleTitle.length > 38) {
              result.articleTitle = result.articleTitle.slice(0, 36) + '...';
            }

            // Sort the results into the title and keyword result stacks.
            if (elBeingSearched.is('h2') || elBeingSearched.is('h3')) {

              // Assume that this is a duplicate and try to prove otherwise.
              var isDuplicate = false;

              for (var j = 0; j < TT.search.titleResults.length; j++) {
                // For each previous title result, check if it's the same as the
                // title result we are trying to add.
                if (TT.search.titleResults[j].articleTitle ==
                    result.articleTitle) {
                  isDuplicate = true;
                }
              }

              // Only add this title result if it's not a duplicate.
              if (!isDuplicate) {
                TT.search.titleResults.push(result);
              }

            }
            else {
              TT.search.keywordResults.push(result);
            }
          }
        }
      }
    }
  });

  TT.search.showResult();
};


/**
 * Show result.
 */
TT.search.showResult = function() {

  // Rest both types of search results.
  TT.search.dropdownTitles.children('ul').remove();
  TT.search.dropdownKeywords.children('ul').remove();

  var hasTitleResults = TT.search.titleResults.length > 0;
  var hasKeywordResults = TT.search.keywordResults.length > 0;

  if (!hasTitleResults) {
    TT.search.dropdownTitles.hide();
  }

  if (!hasKeywordResults) {
    TT.search.dropdownKeywords.hide();
  }

  if (hasKeywordResults || hasTitleResults) {
    TT.search.dropdown.removeClass('no-results').addClass('open');
  }
  else if (TT.search.field.val() != '') {
    TT.search.dropdown.addClass('no-results').addClass('open');
  }

  // Render the title results.
  if (hasTitleResults) {
    var resultHTML = $('<ul/>');

    for (var i = 0; i < Math.min(TT.search.titleResults.length,
        TT.search.THING_RESULTS_LIMIT); i++) {
      var result = TT.search.titleResults[i];

      var li = $('<li/>').mousedown(function() {
        TT.navigation.goToPage($(this).attr('class'), 1);
      });

      li.addClass(result.articleId);
      li.append('<div class="illustration"></div>');
      li.append('<p class="title">#' + result.articleIndex + ' ' +
          result.articleTitle + '</p>');

      // If this chapter consists only of one chapter it needs to be rendered
      // differently.
      if (Math.abs(parseInt(result.articleGlobalStartPage) -
          parseInt(result.articleGlobalEndPage)) != 0) {
        li.append('<p class="pages">' + SERVER_VARIABLES.PAGES + ': ' +
            result.articleGlobalStartPage + '-' + result.articleGlobalEndPage +
            '</p>');
      }
      else {
        li.append('<p class="pages">' + SERVER_VARIABLES.PAGE + ': ' +
            result.articleGlobalStartPage + '</p>');
      }

      resultHTML.append(li);
    }

    TT.search.dropdownTitles.append(resultHTML);
    TT.search.dropdownTitles.show();
  }

  // Render the keyword results.
  if (hasKeywordResults) {
    var resultHTML = $('<ul/>');

    for (var i = 0; i < Math.min(TT.search.keywordResults.length,
        TT.search.KEYWORD_RESULTS_LIMIT); i++) {
      var result = TT.search.keywordResults[i];

      var li = $('<li/>').mousedown(function() {
        TT.navigation.goToPage($(this).attr('data-articleId'),
            $(this).attr('data-articlePage'));
      });

      li.attr('data-articleId', result.articleId);
      li.attr('data-articlePage', result.articlePage);
      li.append('<p class="snippet">"...' + result.snippet + '..."</p>');
      li.append('<p class="pages">' + SERVER_VARIABLES.THING + ' #' +
          result.articleIndex + ' ' + SERVER_VARIABLES.PAGE + ': ' +
          result.articleGlobalStartPage + '</p>');

      resultHTML.append(li);

    }

    TT.search.dropdownKeywords.append(resultHTML);
    TT.search.dropdownKeywords.show();
  }

  TT.search.dropdown.children('.fader')
      .height(TT.search.dropdownResults.outerHeight());
};


/**
 * Hide results.
 */
TT.search.hideResults = function() {
  TT.search.dropdown.removeClass('open');
};
