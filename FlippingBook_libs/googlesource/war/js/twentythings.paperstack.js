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
 * @fileoverview On the right hand side of the book there is an indication of
 * depth depending on how far into the book you are. This manager updates said
 * stack.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.paperstack = {};


/**
 * The HTML element that contains the paper elements.
 */
TT.paperstack.container = null;


/**
 * Initialize paper stack class.
 */
TT.paperstack.initialize = function() {
  TT.paperstack.container = $('#paperstack');
};


/**
 * Updates the number of currently visible papers in the stack that appears on
 * the right side of the book depending on reading progress.
 * @param {number=} overrideProgress If specified, this value (on a range of
 *     0-1) will be used in place of the progress that the currently selected
 *     page reflects.
 */
TT.paperstack.updateStack = function(overrideProgress) {
  var availablePapers = $('div.paper', TT.paperstack.container).length;
  var visiblePapers = Math.round(((1 - (overrideProgress ?
      overrideProgress : TT.chapternav.getProgress())) * availablePapers));

  if (visiblePapers != 0) {
    $('.paper:lt(' + visiblePapers + ')', TT.paperstack.container)
        .css({ opacity: 1 });
    $('.paper:gt(' + visiblePapers + ')', TT.paperstack.container)
        .css({ opacity: 0 });
    $('.shadow', TT.paperstack.container).css({ opacity: 1 });
  }
  else {
    $('.paper', TT.paperstack.container).css({ opacity: 0 });
    $('.shadow', TT.paperstack.container).css({ opacity: 0 });
  }

  $('.shadow', TT.paperstack.container).css({ marginLeft: -9 + visiblePapers });
};
