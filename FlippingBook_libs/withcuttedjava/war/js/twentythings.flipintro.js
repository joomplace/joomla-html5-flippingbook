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
 * @fileoverview Renders a small flipping page animation on the home page that
 * attempts to communicate the type of interaction to expect from the reading
 * experience.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.flipintro = {};


/**
 * Canvas width.
 * @type {number}
 */
TT.flipintro.WIDTH = 89;


/**
 * Canvas height.
 * @type {number}
 */
TT.flipintro.HEIGHT = 29;


/**
 * Canvas vspace.
 * @type {number}
 */
TT.flipintro.VSPACE = 20;


/**
 * Animation loop interval.
 * @type {number}
 */
TT.flipintro.loopInterval = -1;


/**
 * The canvas element.
 * @type {Object}
 */
TT.flipintro.canvas = null;


/**
 * The canvas context.
 * @type {Object}
 */
TT.flipintro.context = null;


/**
 * Flip status flags.
 */
TT.flipintro.flip = {
  progress: 0,
  alpha: 0
};


/**
 * Initialize references and dimensions.
 */
TT.flipintro.initialize = function() {
  TT.flipintro.canvas = $('#flip-intro');

  if (TT.flipintro.canvas[0]) {
    TT.flipintro.canvas[0].width = TT.flipintro.WIDTH;
    TT.flipintro.canvas[0].height = TT.flipintro.HEIGHT +
        (TT.flipintro.VSPACE * 2);
    TT.flipintro.context = TT.flipintro.canvas[0].getContext('2d');
  }
};


/**
 * Activate animation.
 */
TT.flipintro.activate = function() {
  if (TT.flipintro.loopInterval == -1) {
    TT.flipintro.flip.progress = 1;
    TT.flipintro.loopInterval = setInterval(TT.flipintro.render, 32);
  }
};


/**
 * De-activate animation.
 */
TT.flipintro.deactivate = function() {
  clearInterval(TT.flipintro.loopInterval);
  TT.flipintro.loopInterval = -1;
};


/**
 * Animation rendering loop.
 */
TT.flipintro.render = function() {
  if (!TT.flipintro.canvas[0]) {
    return;
  }

  TT.flipintro.context.clearRect(0, 0, TT.flipintro.WIDTH,
      TT.flipintro.HEIGHT + (TT.flipintro.VSPACE * 2));

  // If we are not on the home page, deactivate rendering.
  if (!TT.navigation.isHomePage()) {
    TT.flipintro.deactivate();
  }

  // Ease the progress towards the final (-1) position [ease-in-out].
  TT.flipintro.flip.progress -= Math.max(0.12 *
      (1 - Math.abs(TT.flipintro.flip.progress)), 0.02);

  // Fade in at the start and out at the end.
  TT.flipintro.flip.alpha = 1 -
      ((Math.abs(TT.flipintro.flip.progress) - 0.7) / 0.3);

  // Since the easing is a constant reduction in progress, we can use it as a
  // delay to determine when the progress (and flip) should be reset. Lower
  // number means a longer delay.
  if (TT.flipintro.flip.progress < -2) {
    TT.flipintro.flip.progress = 1;
  }

  var strength = 1 - Math.abs(TT.flipintro.flip.progress);
  var anchorOutdent = strength * 12;
  var controlOutdent = strength * 8;

  // The source position of the page flip (center of the book).
  var source = {
    top: { x: TT.flipintro.WIDTH * 0.5, y: TT.flipintro.VSPACE },
    bottom: { x: TT.flipintro.WIDTH * 0.5, y: TT.flipintro.HEIGHT +
          TT.flipintro.VSPACE}
  };

  // The destination position where the page is current reaching from the
  // source.
  var destination = {
    top: { x: source.top.x + (TT.flipintro.WIDTH * TT.flipintro.flip.progress *
        0.6), y: TT.flipintro.VSPACE - anchorOutdent },
    bottom: { x: source.bottom.x + (TT.flipintro.WIDTH *
        TT.flipintro.flip.progress * 0.6), y: TT.flipintro.HEIGHT +
          TT.flipintro.VSPACE - anchorOutdent }
  };

  // Control position used to bend the page.
  var control = {
    top: { x: source.top.x + (12 * TT.flipintro.flip.progress),
      y: TT.flipintro.VSPACE - controlOutdent },
    bottom: { x: source.bottom.x + (12 * TT.flipintro.flip.progress),
      y: TT.flipintro.HEIGHT + TT.flipintro.VSPACE - controlOutdent }
  };

  TT.flipintro.context.fillStyle = 'rgba(238,238,238,' +
      TT.flipintro.flip.alpha + ')';
  TT.flipintro.context.strokeStyle = 'rgba(90,90,90,' +
      TT.flipintro.flip.alpha + ')';

  TT.flipintro.context.beginPath();
  TT.flipintro.context.moveTo(source.top.x, source.top.y);
  TT.flipintro.context.quadraticCurveTo(control.top.x, control.top.y,
      destination.top.x, destination.top.y);
  TT.flipintro.context.lineTo(destination.bottom.x, destination.bottom.y);
  TT.flipintro.context.quadraticCurveTo(control.bottom.x, control.bottom.y,
      source.bottom.x, source.bottom.y);

  TT.flipintro.context.fill();
  TT.flipintro.context.stroke();

};
