/*!
	reflection.js for jQuery v1.02
	(c) 2006-2008 Christophe Beyls <http://www.digitalia.be>
	MIT-style license.
*/

(function($) {

$.fn.extend({
	reflect: function(options) {
		options = $.extend({
			height: 0.33,
			opacity: 0.5
		}, options);

		return this.unreflect().each(function() {
			var img = this;
			if (/^img$/i.test(img.tagName)) {
				function doReflect() {
					var reflection, reflectionHeight = Math.floor(img.height * options.height), wrapper, context, gradient;

					if ($.browser.msie) {
						reflection = $("<img />").attr("src", img.src).css({
							width: img.width,
							height: img.height,
							marginBottom: -img.height + reflectionHeight,
							filter: "flipv progid:DXImageTransform.Microsoft.Alpha(opacity=" + (options.opacity * 100) + ", style=1, finishOpacity=0, startx=0, starty=0, finishx=0, finishy=" + (options.height * 100) + ")"
						})[0];
					} else {
						reflection = $("<canvas />")[0];
						if (!reflection.getContext) return;
						context = reflection.getContext("2d");
						try {
							$(reflection).attr({width: img.width, height: reflectionHeight});
							context.save();
							context.translate(0, img.height-1);
							context.scale(1, -1);
							context.drawImage(img, 0, 0, img.width, img.height);
							context.restore();
							context.globalCompositeOperation = "destination-out";

							gradient = context.createLinearGradient(0, 0, 0, reflectionHeight);
							gradient.addColorStop(0, "rgba(255, 255, 255, " + (1 - options.opacity) + ")");
							gradient.addColorStop(1, "rgba(255, 255, 255, 1.0)");
							context.fillStyle = gradient;
							context.rect(0, 0, img.width, reflectionHeight);
							context.fill();
						} catch(e) {
							return;
						}
					}
					$(reflection).css({display: "block", border: 0});

					wrapper = $(/^a$/i.test(img.parentNode.tagName) ? "<span />" : "<div />").insertAfter(img).append([img, reflection])[0];
					wrapper.className = img.className;
					$.data(img, "reflected", wrapper.style.cssText = img.style.cssText);
					$(wrapper).css({width: img.width, height: img.height + reflectionHeight, overflow: "hidden"});
					img.style.cssText = "display: block; border: 0px";
					img.className = "reflected";
				}

				if (img.complete) doReflect();
				else $(img).load(doReflect);
			}
		});
	},

	unreflect: function() {
		return this.unbind("load").each(function() {
			var img = this, reflected = $.data(this, "reflected"), wrapper;

			if (reflected !== undefined) {
				wrapper = img.parentNode;
				img.className = wrapper.className;
				img.style.cssText = reflected;
				$.removeData(img, "reflected");
				wrapper.parentNode.replaceChild(img, wrapper);
			}
		});
	}
});

})(jQuery);