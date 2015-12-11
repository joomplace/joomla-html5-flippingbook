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
 * @fileoverview The Cache class manages the offline caching of files. It makes
 * sure any errors are handled appropriately and takes necessary action
 * depending on the state of caching. HTML5's Application Cache is used to cache
 * the app and while doing so it dispatches several events to indicate its
 * status; this manager listens to those events.  This is currently use for
 * debugging purposes.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.cache = {};


/**
 * Initialize cache event listners.
 */
TT.cache.initialize = function() {
  $(window.applicationCache).bind('downloading',
      TT.cache.onDownloadingHandler);
  $(window.applicationCache).bind('progress', TT.cache.onProgressHandler);
  $(window.applicationCache).bind('error', TT.cache.onErrorHandler);
  $(window.applicationCache).bind('cached', TT.cache.onCachedHandler);
  $(window.applicationCache).bind('updateready',
      TT.cache.onUpdateReadyHandler);
  $(window.applicationCache).bind('noupdate', TT.cache.onNoUpdateHandler);
  $(window.applicationCache).bind('obsolete', TT.cache.onObsoleteHandler);
};


/**
 * Called when the browser starts downloading the cache targets.
 * @param {Object} event Cache event object.
 */
TT.cache.onDownloadingHandler = function(event) {
  TT.log('TT.cache.onDownloadingHandler');
};


/**
 * Called when the cache manifest has not changed and no update will be carried
 * out.
 * @param {Object} event Cache event object.
 */
TT.cache.onNoUpdateHandler = function(event) {
  TT.log('TT.cache.onNoUpdateHandler');
};


/**
 * Called as the download progresses.
 * @param {Object} event Cache event object.
 */
TT.cache.onProgressHandler = function(event) {
  TT.log('TT.cache.onProgressHandler');
};


/**
 * Called if an error occurs while downloading any of the cache targets.
 * @param {Object} event Cache event object.
 */
TT.cache.onErrorHandler = function(event) {
  TT.log('TT.cache.onErrorHandler');
};


/**
 * The manifest file could not be found and the cached files will be cleared.
 * @param {Object} event Cache event object.
 */
TT.cache.onObsoleteHandler = function(event) {
  TT.log('TT.cache.onObsoleteHandler');
};


/**
 * Called when caching of the target files is completed.
 * @param {Object} event Cache event object.
 */
TT.cache.onCachedHandler = function(event) {
  TT.log('TT.cache.onCachedHandler');
};


/**
 * Called when re-caching of the target files is completed.
 * @param {Object} event Cache event object.
 */
TT.cache.onUpdateReadyHandler = function(event) {
  TT.log('TT.cache.onCachedHandler');
};
