/*
	reflection.js for mootools v1.2
	by Christophe Beyls (http://www.digitalia.be) - MIT-style license
*/

var Reflection = {

	add: function(img, options){
		img = $(img);
		if (img.getTag() != 'img') return;
		options = {arguments: [img, options]};
		if (window.ie) options.delay = 50;
		img.preload = new Image();
		img.preload.onload = Reflection.reflect.create(options);
		img.preload.src = img.src;
	},

	remove: function(img){
		img = $(img);
		if (img.preload) img.preload.onload = null;
		if ((img.getTag() == 'img') && (img.className == 'reflected')){
			img.className = img.parentNode.className;
			img.style.cssText = img.backupStyle;
			img.parentNode.replaceWith(img);
		}
	},

	reflect: function(img, options){
		options = $extend({
			height: 0.33,
			opacity: 0.5
		}, options || {});

		Reflection.remove(img);
		var canvas, canvasHeight = Math.floor(img.height*options.height);

		if (window.ie){
			canvas = new Element('img', {'src': img.src, 'styles': {
				'width': img.width,
				'marginBottom': -img.height+canvasHeight,
				'filter': 'flipv progid:DXImageTransform.Microsoft.Alpha(opacity='+(options.opacity*100)+', style=1, finishOpacity=0, startx=0, starty=0, finishx=0, finishy='+(options.height*100)+')'
			}});
		} else {
			canvas = new Element('canvas', {'styles': {'width': img.width, 'height': canvasHeight}});
			if (!canvas.getContext) return;
		}

		var div = new Element('div').injectAfter(img).adopt(img, canvas);
		div.className = img.className;
		div.style.cssText = img.backupStyle = img.style.cssText;
		div.removeClass('reflect').setStyles({'width': img.width, 'height': canvasHeight+img.height});
		img.style.cssText = 'vertical-align: bottom';
		img.className = 'reflected';
		if (window.ie) return;

		var context = canvas.setProperties({'width': img.width, 'height': canvasHeight}).getContext('2d');
		context.save();
		context.translate(0, img.height-1);
		context.scale(1, -1);
		context.drawImage(img, 0, 0, img.width, img.height);
		context.restore();
		context.globalCompositeOperation = 'destination-out';
		var gradient = context.createLinearGradient(0, 0, 0, canvasHeight);
		gradient.addColorStop(0, 'rgba(255, 255, 255, '+(1-options.opacity)+')');
		gradient.addColorStop(1, 'rgba(255, 255, 255, 1.0)');
		context.fillStyle = gradient;
		context.rect(0, 0, img.width, canvasHeight);
		context.fill();
	},

	addFromClass: function(){
		$each(document.getElementsByTagName('img'), function(img){
			if ($(img).hasClass('reflect')) Reflection.add(img);
		});
	}
};

Element.extend({
	addReflection: function(options) { Reflection.add(this, options); return this; },
	removeReflection: function(options) { Reflection.remove(this, options); return this; }
});

Window.addEvent("domready", Reflection.addFromClass);
