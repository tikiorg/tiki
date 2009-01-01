/**
Script: Slideshow.Fullsize.js
	Slideshow.Fullsize - Full width / height extension for Slideshow.

License:
	MIT-style license.

Copyright:
	Copyright (c) 2008 [TikiWiki Community](http://www.tikiwiki.org/).
	
Dependencies:
	Slideshow.
*/

Slideshow.Fullsize = new Class({
	Extends: Slideshow,

	options: {
		adjustheight: 0,
		adjustwidth: 0,
		resize: 'length',
		center: false
	},

	initialize: function(el, data, options){
		this.parent(el, data, options);

		window.addEvent('resize', function(){
			this.height = window.innerHeight + this.options.adjustheight;
			this.width = window.innerWidth + this.options.adjustwidth;
			$clear(this.timer);
			this.delay = 0;
			if (this.preloader) this.preloader = this.preloader.destroy();
			this._preload(true);
		}.bind(this));

		if ( this.options.thumbnails ){
			['a', 'b'].each(function(p) {
				new Element('div', { 'class': 'overlay ' + p }).inject(this.slideshow.retrieve('thumbnails'));
			}, this);
		}

	},

	_resize: function(img){
		this.height = window.innerHeight + this.options.adjustheight;
		this.width = window.innerWidth + this.options.adjustwidth;
		var h = this.preloader.get('height'), w = this.preloader.get('width');
		var dh = this.height / h, dw = this.width / w, d = (dh > dw) ? dw : dh;
		var height = Math.ceil(h * d), width = Math.ceil(w * d);

		if ( this.counter > 0 ) {
			var oldimg = (this.counter % 2) ? this.a : this.b;
			oldimg.set('styles', {'left': '50%', 'top': '50%', 'margin-left': this.oldwidth / -2, 'margin-top': this.oldheight / -2});
		}

		this.oldwidth = width;
		this.oldheight = height;
		img.set('styles', {'height': height, 'width': width, 'left': 0, 'top': 0, 'margin-left': 0, 'margin-top': 0});
		$('images').set('styles', {'height': height, 'width': width, 'margin': 'auto', 'overflow': 'visible'});
		$('show').set('styles', {'height': height, 'width': width});
	}
});
