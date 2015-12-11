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
 * @fileoverview Controls the animations which are played for some of the
 * illustrations in the book.
 * @author hakim.elhattab@f-i.com (Hakim El Hattab)
 * @author erik.kallevig@f-i.com (Erik Kallevig)
 */


/**
 * Sub-namespace.
 * @type {Object}
 */
TT.illustrations = {};


/**
 * The frame rate at which the illustration animations will be rendered.
 * @type {number}
 */
TT.illustrations.FRAME_RATE = 30;


/**
 * The location of all our illustration asset images.
 * @type {string}
 */
TT.illustrations.ASSETS_URL = '/media/illustrations/';


/**
 * The single interval that runs to render the animations.
 * @type {number}
 */
TT.illustrations.interval = -1;


/**
 * Updates the component by checking if there is any animation available for
 * the current page, if there is it will be activated otherwise all animations
 * will be deactivated.
 * @param {String} currentPage Current page.
 */
TT.illustrations.update = function(currentPage) {
  TT.illustrations.deactivate();

  if (currentPage && !TT.navigation.isHomePage() &&
      !TT.navigation.isFullScreen()) {

    TT.log('Activate animation: ' + currentPage.attr('class'));

    // HTML/CSS:
    if (currentPage.hasClass('title-html') && currentPage.hasClass('page-3')) {
      TT.illustrations.html.activate($('div.image1', currentPage));
    }

    // Foreword:
    else if (currentPage.hasClass('title-foreword') &&
        currentPage.hasClass('page-1')) {
      TT.illustrations.cloud.activate($('div.image1', currentPage));
    }

    // Open Source:
    else if (currentPage.hasClass('title-open-source') &&
        currentPage.hasClass('page-1')) {
      TT.illustrations.opensource.activate($('div.image1', currentPage));
    }

    // What Is The Internet:
    else if (currentPage.hasClass('title-what-is-the-internet') &&
        currentPage.hasClass('page-1')) {
      TT.illustrations.internet.activate($('div.image1', currentPage));
    }

    // Page Load:
    else if (currentPage.hasClass('title-page-load') &&
        currentPage.hasClass('page-1')) {
      TT.illustrations.pageload.activate($('div.image1', currentPage));
    }

    // Web Apps:
    else if (currentPage.hasClass('title-web-apps') &&
        currentPage.hasClass('page-1')) {
      TT.illustrations.webapps.activate($('div.image1', currentPage));
    }

    // 3D:
    else if (currentPage.hasClass('title-threed') &&
        currentPage.hasClass('page-1')) {
      TT.illustrations.threed.activate($('div.image1', currentPage));
    }

  }
};


/**
 * Deactivates rendering of all illustration animations.
 */
TT.illustrations.deactivate = function() {
  clearInterval(TT.illustrations.interval);
};


/**
 * Creates a new canvas element with the specified properties.
 * @param {Object} parent Parent node.
 * @param {Object} world World stage.
 * @return {Object} Canvas element.
 */
TT.illustrations.createCanvas = function(parent, world) {
  var canvas = $('<canvas></canvas>');

  canvas[0].width = world.width;
  canvas[0].height = world.height;

  parent.append(canvas);

  TT.illustrations.updateCanvasPosition(parent, world);

  return canvas;
};


/**
 * Creates a new canvas element with the specified properties.
 * @param {Object} parent Parent node.
 * @param {Object} world World stage.
 * @return {boolean} If image width or height is equal to 0.
 */
TT.illustrations.updateCanvasPosition = function(parent, world) {
  var canvas = $('canvas', parent);

  canvas.css({
    position: 'absolute',
    left: $('img', parent).position().left + world.x,
    top: $('img', parent).position().top + world.y
  });

  return $('img', parent).width() !== 0 || $('img', parent).height() !== 0;
};


/**
 * 3D illustration.
 * @type {Object}
 */
TT.illustrations.threed = {

  // DOM & API elements.
  canvas: null,
  context: null,
  container: null,
  image: null,
  cloudImage: null,
  clouds: [],
  alpha: 0,

  // Defines the dimensions and position of the world which this animation is
  // drawn in.
  world: { x: 0, y: 0, width: 320, height: 150 },

  // Flags if the position for the canvas has been set.
  positioned: false,


  /**
   * Initialize.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  initialize: function(container) {

    // Only initialize once.
    if (this.canvas === null) {

      // Store references to the container and image we are animating.
      this.container = container;
      this.image = $('img', container);

      this.canvas = TT.illustrations.createCanvas(container, this.world);
      this.context = this.canvas[0].getContext('2d');

      this.cloudImage = new Image();
      this.cloudImage.src = TT.illustrations.ASSETS_URL + '3d01_clouds.png';

      this.clouds.push({
        source: { x: 0, y: 0, width: 63, height: 35 },
        x: 44,
        y: 16,
        originalX: 44,
        originalY: 16,
        velocity: { x: 0, y: 0 }
      });

      this.clouds.push({
        source: { x: 0, y: 36, width: 70, height: 40 },
        x: 147,
        y: 10,
        originalX: 147,
        originalY: 10,
        velocity: { x: 0, y: 0 }
      });

      this.clouds.push({
        source: { x: 0, y: 78, width: 84, height: 50 },
        x: 213,
        y: 48,
        originalX: 212,
        originalY: 48,
        velocity: { x: 0, y: 0 }
      });

    }
    else {
      this.positioned = TT.illustrations.updateCanvasPosition(this.container,
          this.world);
    }
  },


  /**
   * Activates rendering for this animation.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  activate: function(container) {
    this.initialize(container);

    TT.illustrations.interval =
        setInterval(this.render, 1000 / TT.illustrations.FRAME_RATE);
  },


  /**
   * Handler for the interval used to draw the animation.
   */
  render: function() {
    TT.illustrations.threed.draw();
  },


  /**
   * Draws the current state of this animation.
   * @this {Object} Illustration class.
   */
  draw: function() {
    if (!this.positioned) {
      this.positioned =
          TT.illustrations.updateCanvasPosition(this.container, this.world);
    }

    this.context.clearRect(0, 0, this.world.width, this.world.height);

    if (this.cloudImage.complete) {

      this.alpha = Math.min(this.alpha + 0.1, 1);
      this.context.globalAlpha = this.alpha;

      // Go through and update/draw each cloud.
      for (var i = 0; i < this.clouds.length; i++) {

        var cloud = this.clouds[i];

        cloud.x += cloud.velocity.x;
        cloud.y += cloud.velocity.y;

        cloud.velocity.x *= 0.96;
        cloud.velocity.y *= 0.96;

        var speed = 0.3;

        if (Math.abs(cloud.velocity.x) < 0.01) {
          if (cloud.x > cloud.originalX) {
            cloud.velocity.x -= 0.05 + Math.random() * speed;
          }
          else {
            cloud.velocity.x += 0.05 + Math.random() * speed;
          }
        }

        if (Math.abs(cloud.velocity.y) < 0.01) {
          if (cloud.y > cloud.originalY) {
            cloud.velocity.y -= 0.01 + Math.random() * speed;
          }
          else {
            cloud.velocity.y += 0.01 + Math.random() * speed;
          }
        }

        this.context.save();

        this.context.translate(cloud.x, cloud.y);
        this.context.drawImage(this.cloudImage, cloud.source.x, cloud.source.y,
            cloud.source.width, cloud.source.height, 0, 0, cloud.source.width,
            cloud.source.height);

        this.context.restore();

      }
    }
  }
};


/**
 * Web Apps illustration.
 * @type {Object}
 */
TT.illustrations.webapps = {

  GRAVITY: 0.04,

  // DOM & API elements
  canvas: null,
  context: null,
  container: null,
  image: null,

  leaves: [],
  points: [
    { x: 86, y: 100 },
    { x: 35, y: 88 },
    { x: 168, y: 35 },
    { x: 250, y: 15 }
  ],

  // Defines the dimensions and position of the world which this animation is
  // drawn in.
  world: { x: 20, y: 30, width: 300, height: 260 },

  // Flags if the position for the canvas has been set.
  positioned: false,


  /**
   * Initialize illustration.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  initialize: function(container) {

    // Only initialize once.
    if (this.canvas === null) {

      // Store references to the container and image we are animating.
      this.container = container;
      this.image = $('img', container);

      this.canvas = TT.illustrations.createCanvas(container, this.world);
      this.context = this.canvas[0].getContext('2d');
    }
    else {
      this.positioned =
          TT.illustrations.updateCanvasPosition(this.container, this.world);
    }
  },


  /**
   * Activates rendering for this animation.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  activate: function(container) {
    this.initialize(container);

    TT.illustrations.interval =
        setInterval(this.render, 1000 / TT.illustrations.FRAME_RATE);
  },


  /**
   * Handler for the interval used to draw the animation.
   */
  render: function() {
    TT.illustrations.webapps.draw();
  },


  /**
   * Draws the current state of this animation.
   * @this {Object} Illustration class.
   */
  draw: function() {
    if (!this.positioned) {
      this.positioned =
          TT.illustrations.updateCanvasPosition(this.container, this.world);
    }

    this.context.clearRect(0, 0, this.world.width, this.world.height);

    // Add leaves if needed.
    if (this.leaves.length < 4 && Math.random() > 0.9) {
      var point = this.points[Math.floor(Math.random() * this.points.length)];

      this.leaves.push({
        x: point.x,
        y: point.y,
        w: 18 + (Math.random() * 8),
        h: 6 + (Math.random() * 4),
        alpha: 0,
        rotation: Math.random() * Math.PI,
        velocity: { x: -0.2 + (Math.random() * 0.4), y: Math.random() * 2,
          rotation: -0.1 + (Math.random() * 0.2) }
      });
    }

    // Go through and update/draw each leaves.
    for (var i = 0; i < this.leaves.length; i++) {

      var leaf = this.leaves[i];

      // Appy gravity.
      leaf.velocity.y += this.GRAVITY;

      if (leaf.y > this.world.height + 20) {
        this.leaves.splice(i, 1);
        i--;
        continue;
      }

      leaf.x += leaf.velocity.x;
      leaf.y += leaf.velocity.y;
      leaf.rotation += leaf.velocity.rotation;
      leaf.alpha = Math.min(leaf.alpha + 0.1, 1);

      this.context.save();

      var b = 3;

      this.context.globalAlpha = leaf.alpha;
      this.context.beginPath();
      this.context.translate(leaf.x, leaf.y);
      this.context.rotate(leaf.rotation);
      this.context.strokeStyle = 'rgba(0,100,20,0.7)';
      this.context.fillStyle = 'rgba(159,192,94,0.9)';
      this.context.moveTo(0, leaf.h / 2);
      this.context.quadraticCurveTo(leaf.w / 2, -b, leaf.w, leaf.h / 2);
      this.context.quadraticCurveTo(leaf.w / 2, leaf.h + b, 0, leaf.h / 2);
      this.context.stroke();
      this.context.fill();

      this.context.restore();

    }

    var mask = this.context.createLinearGradient(0, 0, 0, this.world.height);
    mask.addColorStop(0.7, 'rgba(255, 255, 255, 0)');
    mask.addColorStop(1.0, 'rgba(255, 255, 255, 1)');

    this.context.save();
    this.context.globalCompositeOperation = 'destination-out';
    this.context.beginPath();
    this.context.fillStyle = mask;
    this.context.fillRect(0, 0, this.world.width, this.world.height);
    this.context.restore();

  }

};


/**
 * Page load illustration.
 * @type {Object}
 */
TT.illustrations.pageload = {

  GRAVITY: 0.04,

  // DOM & API elements.
  canvas: null,
  context: null,
  container: null,
  image: null,

  bubbles: [],

  // Defines the dimensions and position of the world which this animation is
  // drawn in.
  world: { x: 10, y: 100, width: 220, height: 100 },

  // Flags if the position for the canvas has been set.
  positioned: false,


  /**
   * Initialize illustration.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  initialize: function(container) {

    // Only initialize once.
    if (this.canvas === null) {

      // Store references to the container and image we are animating.
      this.container = container;
      this.image = $('img', container);

      this.canvas = TT.illustrations.createCanvas(container, this.world);
      this.context = this.canvas[0].getContext('2d');
    }
    else {
      this.positioned =
          TT.illustrations.updateCanvasPosition(this.container, this.world);
    }
  },


  /**
   * Activates rendering for this animation.
   * @this {Object} Illustration class.
   */
  activate: function(container) {
    this.initialize(container);

    TT.illustrations.interval =
        setInterval(this.render, 1000 / TT.illustrations.FRAME_RATE);
  },


  /**
   * Handler for the interval used to draw the animation.
   */
  render: function() {
    TT.illustrations.pageload.draw();
  },


  /**
   * Draws the current state of this animation.
   * @this {Object} Illustration class.
   */
  draw: function() {
    if (!this.positioned) {
      this.positioned =
          TT.illustrations.updateCanvasPosition(this.container, this.world);
    }

    this.context.clearRect(0, 0, this.world.width, this.world.height);

    // Add bubbles if needed.
    if (this.bubbles.length < 7 && Math.random() > 0.85) {
      this.bubbles.push({
        x: Math.random() * this.world.width,
        y: this.world.height + 10,
        alpha: 0,
        size: 1 + Math.random() * 3,
        velocity: { x: -0.4 + (Math.random() * 0.8), y: Math.random() * -2 }
      });
    }

    // Go through and update/draw each bubbles.
    for (var i = 0; i < this.bubbles.length; i++) {

      var bubble = this.bubbles[i];

      // Appy gravity.
      bubble.velocity.y -= this.GRAVITY;

      if (bubble.y < -10) {
        this.bubbles.splice(i, 1);
        i--;
        continue;
      }
      else if (bubble.y < this.world.height * 0.3) {
        bubble.alpha = Math.max(bubble.y / (this.world.height * 0.3), 0);
      }
      else if (bubble.y > this.world.height * 0.7) {
        bubble.alpha = 1 - Math.min((bubble.y - this.world.height * 0.7) /
            (this.world.height * 0.3), 1);
      }

      bubble.x += bubble.velocity.x;
      bubble.y += bubble.velocity.y;

      this.context.beginPath();
      this.context.strokeStyle =
          'rgba( 0, 0, 0, ' + (bubble.alpha * 0.3) + ' )';
      this.context.fillStyle =
          'rgba( 0, 180, 250, ' + (bubble.alpha * 0.7) + ' )';
      this.context.arc(bubble.x, bubble.y, bubble.size, 0, Math.PI * 2, true);
      this.context.stroke();

    }

    var mask =
        this.context.createLinearGradient(0, 0, -25, this.world.height);
    mask.addColorStop(0.0, 'rgba(255, 255, 255, 1)');
    mask.addColorStop(0.6, 'rgba(255, 255, 255, 0)');

    this.context.save();
    this.context.globalCompositeOperation = 'destination-out';
    this.context.beginPath();
    this.context.fillStyle = mask;
    this.context.fillRect(0, 0, this.world.width, this.world.height);
    this.context.restore();

  }

};


/**
 * Internet illustration.
 * @type {Object}
 */
TT.illustrations.internet = {

  GRAVITY: 0.04,

  // DOM & API elements.
  canvas: null,
  context: null,
  container: null,
  image: null,
  zero: null, // HTML Image element.
  one: null, // HTML Image element.

  numbers: [],

  // Flags if the position for the canvas has been set.
  positioned: false,

  // Defines the dimensions and position of the world which this animation is
  // drawn in.
  world: { x: 345, y: 115, width: 120, height: 80 },


  /**
   * Initialize illustration.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  initialize: function(container) {

    // Only initialize once.
    if (this.canvas === null) {

      // Store references to the container and image we are animating.
      this.container = container;
      this.image = $('img', container);

      this.canvas = TT.illustrations.createCanvas(container, this.world);
      this.context = this.canvas[0].getContext('2d');

      this.zero = new Image();
      this.zero.src = TT.illustrations.ASSETS_URL + 'internet01-part1.png';

      this.one = new Image();
      this.one.src = TT.illustrations.ASSETS_URL + 'internet01-part2.png';
    }
    else {
      this.positioned = TT.illustrations.updateCanvasPosition(this.container,
          this.world);
    }
  },


  /**
   * Activates rendering for this animation.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  activate: function(container) {
    this.initialize(container);

    TT.illustrations.interval =
        setInterval(this.render, 1000 / TT.illustrations.FRAME_RATE);
  },


  /**
   * Handler for the interval used to draw the animation.
   */
  render: function() {
    TT.illustrations.internet.draw();
  },


  /**
   * Draws the current state of this animation.
   * @this {Object} Illustration class.
   */
  draw: function() {
    if (!this.positioned) {
      this.positioned =
          TT.illustrations.updateCanvasPosition(this.container, this.world);
    }

    this.context.clearRect(0, 0, this.world.width, this.world.height);

    if (this.zero.complete && this.one.complete) {

      // Add numbers if needed.
      if (this.numbers.length < 20 && Math.random() > 0.6) {
        this.numbers.push({
          type: Math.random() > 0.5 ? 1 : 0,
          x: 5,
          y: 0,
          alpha: 0,
          rotation: Math.random() * Math.PI,
          velocity: {
            x: 0.4 + (Math.random() * 1.6),
            y: Math.random() * 2,
            rotation: -0.1 + (Math.random() * 0.2)
          }
        });
      }

      // Go through and update/draw each number.
      for (var i = 0; i < this.numbers.length; i++) {

        var number = this.numbers[i];
        var image = number.type == 0 ? this.zero : this.one;

        // Appy gravity.
        number.velocity.y += this.GRAVITY;

        if (number.y > this.world.height + image.height) {
          this.numbers.splice(i, 1);
          i--;
          continue;
        }
        else;
        if (number.y < this.world.height * 0.1) {
          number.alpha = Math.min(number.y / (this.world.height * 0.1), 1);
        }
        else;
        if (number.y > this.world.height * 0.6) {
          number.alpha = 1 - Math.min((number.y - this.world.height * 0.5) /
              (this.world.height * 0.3), 1);
        }

        number.x += number.velocity.x;
        number.y += number.velocity.y;
        number.rotation += number.velocity.rotation;

        this.context.save();

        this.context.globalAlpha = number.alpha;
        this.context.translate(number.x + Math.round(image.width * 0.5),
            number.y + Math.round(image.height * 0.5));
        this.context.rotate(number.rotation);
        this.context.translate(-Math.round(image.width * 0.5),
            -Math.round(image.height * 0.5));
        this.context.drawImage(image, 0, 0);

        this.context.restore();

      }
    }
  }
};


/**
 * Open source illustration.
 * @type {Object}
 */
TT.illustrations.opensource = {

  // DOM & API elements.
  canvas: null,
  context: null,
  container: null,
  image: null,
  cog1: {
    image: null,
    x: 57,
    y: 14,
    currentRotation: 0,
    targetRotation: 0,
    lastUpdate: 0
  },
  cog2: {
    image: null,
    x: 28,
    y: 38,
    currentRotation: 0,
    targetRotation: 0,
    lastUpdate: 0
  },
  alpha: 0,

  // Defines the dimensions and position of the world which this animation is
  // drawn in.
  world: { x: 90, y: 37, width: 100, height: 100 },

  // Flags if the position for the canvas has been set.
  positioned: false,


  /**
   * Initialize illustration.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  initialize: function(container) {

    // Only initialize once.
    if (this.canvas === null) {

      // Store references to the container and image we are animating.
      this.container = container;
      this.image = $('img', container);

      this.canvas = TT.illustrations.createCanvas(container, this.world);
      this.context = this.canvas[0].getContext('2d');

      this.cog1.image = new Image();
      this.cog1.image.src = TT.illustrations.ASSETS_URL +
          'opensource01-part1.png';

      this.cog2.image = new Image();
      this.cog2.image.src = TT.illustrations.ASSETS_URL +
          'opensource01-part2.png';
    }
    else {
      this.positioned = TT.illustrations.updateCanvasPosition(this.container,
          this.world);
    }
  },


  /**
   * Activates rendering for this animation.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  activate: function(container) {
    this.initialize(container);

    this.cog1.lastUpdate = TT.time();
    this.cog2.lastUpdate = TT.time();

    TT.illustrations.interval =
        setInterval(this.render, 1000 / TT.illustrations.FRAME_RATE);
  },


  /**
   * Handler for the interval used to draw the animation.
   */
  render: function() {
    TT.illustrations.opensource.draw();
  },


  /**
   * Draws the current state of this animation.
   * @this {Object} Illustration class.
   */
  draw: function() {
    this.context.clearRect(0, 0, this.world.width, this.world.height);

    if (!this.positioned) {
      this.positioned = TT.illustrations.updateCanvasPosition(this.container,
          this.world);
    }

    if (this.cog1.image.complete && this.cog2.image.complete) {

      this.alpha = Math.min(this.alpha + 0.08, 1);

      if (this.cog1.currentRotation > this.cog1.targetRotation - 1 &&
          TT.time() - this.cog1.lastUpdate > 2000) {
        this.cog1.targetRotation += Math.PI / 3;
        this.cog1.lastUpdate = TT.time();
      }

      if (this.cog2.currentRotation > this.cog2.targetRotation - 1 &&
          TT.time() - this.cog2.lastUpdate > 6000) {
        this.cog2.targetRotation += Math.PI / 9;
        this.cog2.lastUpdate = TT.time();
      }

      this.cog1.currentRotation +=
          (this.cog1.targetRotation - this.cog1.currentRotation) * 0.5;
      this.cog2.currentRotation +=
          (this.cog2.targetRotation - this.cog2.currentRotation) * 0.4;

      if (Math.abs(this.cog1.currentRotation -
          this.cog1.targetRotation) < 0.05) {
        this.cog1.currentRotation = this.cog1.targetRotation;
      }

      if (Math.abs(this.cog2.currentRotation -
          this.cog2.targetRotation) < 0.05) {
        this.cog2.currentRotation = this.cog2.targetRotation;
      }

      this.context.save();
      this.context.globalAlpha = this.alpha;

      this.context.save();
      this.context.translate(this.cog1.x + Math.round(this.cog1.image.width *
          0.5), this.cog1.y + Math.round(this.cog1.image.height * 0.5));
      this.context.rotate(this.cog1.currentRotation);
      this.context.translate(-Math.round(this.cog1.image.width * 0.5),
          -Math.round(this.cog1.image.height * 0.5));
      this.context.drawImage(this.cog1.image, 0, 0);
      this.context.restore();

      this.context.save();
      this.context.translate(this.cog2.x + Math.round(this.cog2.image.width *
          0.5), this.cog2.y + Math.round(this.cog2.image.height * 0.5));
      this.context.rotate(this.cog2.currentRotation);
      this.context.translate(-Math.round(this.cog2.image.width * 0.5),
          -Math.round(this.cog2.image.height * 0.5));
      this.context.drawImage(this.cog2.image, 0, 0);
      this.context.restore();

      this.context.restore();

    }
  }
};


/**
 * Foreword illustration.
 * @type {Object}
 */
TT.illustrations.cloud = {

  // DOM & API elements.
  canvas: null,
  context: null,
  container: null,
  image: null,

  cloutImage: null,

  // Defines the dimensions and position of the world which this animation is
  // drawn in.
  world: { x: 0, y: 0, width: 240, height: 200 },

  // Flags if the position for the canvas has been set.
  positioned: false,

  clouds: [{
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }, {
    x: Math.random() * 240,
    y: Math.random() * 200,
    strength: 1,
    velocity: {
      x: 0,
      y: 0
    },
    size: 8 + Math.random() * 8
  }],


  /**
   * Initialize illustration.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  initialize: function(container) {

    // Only initialize once.
    if (this.canvas === null) {

      // Store references to the container and image we are animating.
      this.container = container;
      this.image = $('img', container);

      this.canvas = TT.illustrations.createCanvas(container, this.world);
      this.context = this.canvas[0].getContext('2d');

      this.cloudImage = new Image();
      this.cloudImage.src = TT.illustrations.ASSETS_URL + '3d01_clouds.png';
    }
    else {
      this.positioned = TT.illustrations.updateCanvasPosition(this.container,
          this.world);
    }
  },


  /**
   * Activates rendering for this animation.
   * @this {Object} Illustration class.
   */
  activate: function(container) {
    this.initialize(container);

    TT.illustrations.interval =
        setInterval(this.render, 1000 / TT.illustrations.FRAME_RATE);
  },


  /**
   * Handler for the interval used to draw the animation.
   */
  render: function() {
    TT.illustrations.cloud.draw();
  },


  /**
   * Draws the current state of this animation.
   * @this {Object} Illustration class.
   */
  draw: function() {
    if (!this.positioned) {
      this.positioned =
          TT.illustrations.updateCanvasPosition(this.container, this.world);
    }

    this.context.clearRect(0, 0, this.world.width, this.world.height);

    if (this.image[0].complete) {
      this.context.drawImage(this.image[0], 0, 0);
    }

    var speed = 3;

    // Draw each cloud bulb.
    for (var i = 0, len = this.clouds.length; i < len; i++) {
      var cloud = this.clouds[i];

      cloud.velocity.x /= 1.04;
      cloud.velocity.y /= 1.04;

      if (Math.abs(cloud.velocity.x) < 0.2) {
        if (cloud.x > this.world.width / 2) {
          cloud.velocity.x -= 0.7 + Math.random() * speed;
        }
        else {
          cloud.velocity.x += 0.7 + Math.random() * speed;
        }
      }

      if (Math.abs(cloud.velocity.y) < 0.2) {
        if (cloud.y > this.world.height / 2) {
          cloud.velocity.y -= 0.5 + Math.random() * speed;
        }
        else {
          cloud.velocity.y += 0.5 + Math.random() * speed;
        }
      }

      cloud.x += cloud.velocity.x;
      cloud.y += cloud.velocity.y;

      cloud.strength = Math.max(Math.min(cloud.strength, 1), 0);

      var gradient = this.context.createRadialGradient(cloud.x, cloud.y, 0,
          cloud.x, cloud.y, cloud.size);

      this.context.save();

      var browser = BrowserDetect.browser.toLowerCase();

      // TODO: Remove shameless browser sniffing... FireFox 3.6 does not
      // draw the "cloud's" inside of the bitmap even if the correct
      // composite operation is set.
      if (browser == 'firefox') {
        gradient.addColorStop(0.4, 'rgba(255,255,255,' +
            (cloud.strength * 0.7) + ')');
        gradient.addColorStop(1.0, 'rgba(255,255,255,0)');
      }
      else {
        gradient.addColorStop(0.4, 'rgba(90,170,190,' +
            (cloud.strength) + ')');
        gradient.addColorStop(1.0, 'rgba(90,170,190,0)');

        this.context.globalCompositeOperation = 'source-in';
      }

      this.context.beginPath();
      this.context.fillStyle = gradient;
      this.context.arc(cloud.x, cloud.y, cloud.size, 0, Math.PI * 2, true);
      this.context.fill();

      this.context.restore();
    }
  }
};


/**
 * HTML/CSS "Make Up" illustration.
 * @type {Object}
 */
TT.illustrations.html = {

  // DOM & API elements.
  canvas: null,
  context: null,
  container: null,
  image: null,

  // Defines the dimensions and position of the world which this animation is
  // drawn in.
  world: { x: 100, y: -15, width: 150, height: 200 },

  bulbs: [
    { x: 27, y: 22, strength: 0, momentum: 0, size: 10 },
    { x: 62, y: 30, strength: 0, momentum: 0, size: 10 },
    { x: 90, y: 39, strength: 0, momentum: 0, size: 10 },
    { x: 117, y: 48, strength: 0, momentum: 0, size: 10 },
    { x: 22, y: 59, strength: 0, momentum: 0, size: 10 },
    { x: 23, y: 89, strength: 0, momentum: 0, size: 10 },
    { x: 24, y: 115, strength: 0, momentum: 0, size: 10 },
    { x: 25, y: 139, strength: 0, momentum: 0, size: 10 },
    { x: 124, y: 87, strength: 0, momentum: 0, size: 10 },
    { x: 124, y: 116, strength: 0, momentum: 0, size: 10 },
    { x: 124, y: 144, strength: 0, momentum: 0, size: 10 },
    { x: 124, y: 178, strength: 0, momentum: 0, size: 10 }
  ],


  /**
   * Initialize illustration.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  initialize: function(container) {

    // Only initialize once.
    if (this.canvas === null) {

      // Store references to the container and image we are animating.
      this.container = container;
      this.image = $('img', container);

      this.canvas = TT.illustrations.createCanvas(container, this.world);
      this.context = this.canvas[0].getContext('2d');
    }
    else {
      TT.illustrations.updateCanvasPosition(this.container, this.world);
    }
  },


  /**
   * Activates rendering for this animation.
   * @param {Object} container Container node.
   * @this {Object} Illustration class.
   */
  activate: function(container) {
    this.initialize(container);

    TT.illustrations.interval =
        setInterval(this.render, 1000 / TT.illustrations.FRAME_RATE);
  },


  /**
   * Handler for the interval used to draw the animation.
   */
  render: function() {
    TT.illustrations.html.draw();
  },


  /**
   * Draws the current state of this animation.
   * @this {Object} Illustration class.
   */
  draw: function() {
    this.context.clearRect(0, 0, this.world.width, this.world.height);

    // Draw each light bulb.
    for (var i = 0, len = this.bulbs.length; i < len; i++) {
      var bulb = this.bulbs[i];

      // The following if case will cause an indefinite, randomly delayed, loop
      // in bulb strength.
      if (bulb.strength < 0.1 && bulb.momentum <= 0 && Math.random() > 0.98) {
        bulb.momentum = Math.random() * 0.3;
      }
      else if (bulb.strength >= 1 && Math.random() > 0.98) {
        bulb.momentum = -Math.random() * 0.3;
      }

      bulb.strength += bulb.momentum;
      bulb.strength = Math.max(Math.min(bulb.strength, 1), 0);

      var gradient = this.context.createRadialGradient(bulb.x, bulb.y, 0,
          bulb.x, bulb.y, bulb.size);
      gradient.addColorStop(0.4, 'rgba(255,255,100,' +
          (bulb.strength * 0.95) + ')');
      gradient.addColorStop(1.0, 'rgba(255,255,100,0)');

      this.context.beginPath();
      this.context.fillStyle = gradient;
      this.context.arc(bulb.x, bulb.y, bulb.size, 0, Math.PI * 2, true);
      this.context.fill();
    }

    /* Adds sparkles to the image in the mirror.

    len = 10;

    while ( --len ) {
      var p = { x: this.world.width * 0.3, y: this.world.height * 0.35 };

      p.x += Math.random() * ( this.world.width * 0.4 );
      p.y += Math.random() * ( this.world.height * 0.46 );

      this.context.beginPath();
      this.context.fillStyle = 'rgba(255,255,50,0.6)';
      this.context.fillRect(p.x, p.y, 2, 2 );
      this.context.fill();
    }

    */

  }

};
