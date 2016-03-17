// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Author: bertrand Gugger

/**
 * Image/Slideshow handler
 * You must instantiate it naming the handler the same as the id of the image to anime:
 * var thepix = new Diaporama('thepix', arrPixId, options);
 * - arrPixId is an array of images Id
 * - options is an object with optional properties:
 *   - startId: the image id to start with
 *   - root: a document element (or document itself) or its id as string
 *     If given , by start its children elements of class "noslideshow" will be hidden
 *     and those of class "slideshow" or "slideshow_i" will be display: block or inline
 *     By image change , the children of class "pixurl" are updated with current image id
 *   - resetUrl: if set the document url will be updated with current id by slide stop
 *   - delay: sliding delay in milliseconds , default 3000
 */

function Diaporama(imgName, arrPixId, options)
{
    this.name = imgName;
    this.arrPixId = arrPixId;
    this.length = arrPixId.length;
    this.startId = 0;
    this.delay = 3000;
    this.sliding = 0;
    this.backward = 0;
    this.toTheEnd = 0;
    for (var opt in options) {
        this[opt] = options[opt];
    }
	for (this.curPix = 0;
		 this.curPix < this.length && arrPixId[this.curPix] !== this.startId;
		 this.curPix++) {}
	this.curPix = this.curPix % this.length;

    this.hopePix = 0;
    this.thePix = null;
    this.timerPix = null;
    this.arrPix = [];
}
Diaporama.prototype.setClassStyle = function (className, prop, val) {
    var elts = this.root.all ? this.root.all : this.root.getElementsByTagName('*');
    var reg = new RegExp("(^|\\s)" + className + "(\\s|$)");
    for (var i = 0; i < elts.length; i++) {
        if (reg.test(elts[i].className)) {
            elts[i].style[prop] = val;
        }   
    }
};
Diaporama.prototype.toggle = function (modPix) {
    clearTimeout(this.timerPix);
    switch (modPix) {
      case 'stop':
        this.sliding = 0;
        if (this.resetUrl) {
	        document.location.href = 
	            document.location.href.replace(
								/imageid=\d+/i, 'imageId=' + this.arrPixId[this.curPix]);
				}
        return;
      case 'backward':
        this.backward = ! this.backward;
        break;
      case 'toTheEnd':
        this.toTheEnd = ! this.toTheEnd;
        break;
    }
    this.sliding = 1;
    if (this.root) {
	    if (typeof(this.root) === 'string') {
		    this.root = document.getElementById(this.root);
	    }
	    this.setClassStyle('noslideshow', 'display', 'none');
	    this.setClassStyle('slideshow', 'display', 'block');
	    this.setClassStyle('slideshow_i', 'display', 'inline');
    }
    this.thePix = document.getElementById(this.name);
    this.arrPix[this.curPix] = new Image();
    this.arrPix[this.curPix].src = this.thePix.src;
    this.nextPix();
};
Diaporama.prototype.nextPix = function () {
    this.hopePix = (this.curPix + (this.backward ? this.length - 1 : 1)) % this.length;
    if (!this.arrPix[this.hopePix]) {
        this.arrPix[this.hopePix] = new Image();
        this.arrPix[this.hopePix].src = this.thePix.src.replace(/\?id=\d+&/, '?id=' + this.arrPixId[this.hopePix] + '&');
    }
    this.timerPix = setTimeout(this.name + '.putPix();', this.delay);
};
Diaporama.prototype.setUrlPix = function () {
    var elts = this.root.all ? this.root.all : this.root.getElementsByTagName('*');
    var reg = new RegExp("(^|\\s)pixurl(\\s|$)");
    for (var i = 0; i < elts.length; i++) {
        if (reg.test(elts[i].className)) {
            if (elts[i].href) {
                elts[i].href = elts[i].href.replace(/imageid=\d+/i, 'imageId=' + this.arrPixId[this.curPix]);
                elts[i].href = elts[i].href.replace(/edit=\d+/i, 'edit=' + this.arrPixId[this.curPix]);
            } else {
                elts[i].innerHTML = elts[i].innerHTML.replace(/#\d+/i, '#' + this.arrPixId[this.curPix]);
            }
        }   
    }
};
Diaporama.prototype.putPix = function () {
    if (!this.arrPix[this.curPix].complete) {
        this.timerPix = setTimeout(this.name + '.putPix();', 100);
        return;
    }
    this.curPix = this.hopePix;
    this.thePix.src = this.arrPix[this.curPix].src;
    if (this.root) {
	    this.setUrlPix();
    }
    if (!this.toTheEnd || (this.backward && this.curPix)
                       || (!this.backward && ((this.curPix + 1) % this.length))) {
        this.nextPix();
    } else {
        thepix.toggle('stop');
    }
};
