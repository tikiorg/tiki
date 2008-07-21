/*
Script: Drag.ResizeImage.js
	Utility class for making images resizable.
	Contains <Drag.ResizeImage>.
*/

/*
Class: Drag.ResizeImage
	Creates <Drag.Resize> wrapper instance around the image element.

Arguments:
	el - the image $(element) to apply the resize to.
	options - see <Drag.Resize> options.
*/

Drag.ResizeImage = new Class({

	initialize: function(el, options){
		this.image = $(el);
		this.styles = this.image.getStyles('position', 'top', 'left', 'right', 'bottom', 'z-index', 'margin');
		if (!['absolute', 'fixed', 'relative'].contains(this.styles.position)) this.styles.position = 'relative';
		this.wrapper = new Element('div', {'styles': $merge(this.styles, {
			'width': this.image.offsetWidth,
			'height': this.image.offsetHeight
		})}).injectBefore(this.image).adopt(
			this.image.remove().setStyles({'position': 'absolute', 'top':'0', 'left':'0', 'margin':'0', 'width': '100%', 'height': '100%', 'zIndex': '0'})
		);
		this.fx = new Drag.Resize(this.wrapper, $merge({'preserveRatio': true}, options));
	},

	/*
	Property: stop
		Stop the effect and restore the image element with new size.
	*/

	stop: function(){
		this.image.setStyles($merge(this.styles, {'width': this.wrapper.getStyle('width'), 'height': this.wrapper.getStyle('height')})).remove().injectBefore(this.wrapper);
		this.fx = null;
		this.wrapper.remove(true);
	}

});
