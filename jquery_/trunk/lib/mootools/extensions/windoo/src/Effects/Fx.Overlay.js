/*
Script: Fx.Overlay.js
	Utility class for covering target element or browser window with overlay element. Overlay utility to fix IE6 select tag bug. <Element::remove> modified accordingly.
	Contains <Fx.Overlay>, <Element::fixOverlay>.
*/

/*
Class: Fx.Overlay
	Overlay class to cover target element content.
*/

Fx.Overlay = new Class({

	options: {
		'styles': {
			'position': 'absolute',
			'top': 0,
			'left': 0
		}
	},

	/*
	Property: initialize
		Creates a new Fx.Overlay object.

	Arguments:
		element - element; container element or window object.
		props - object; the properties to set for overlay element. see Element properties.
	*/

	initialize: function(element, props, tag){
		this.element = $(element);
		this.setOptions(props);
		if ([window, $(document.body)].contains(this.element)){
			this.padding =  Fx.Overlay.windowPadding;
			this.container = $(document.body);
			this.element = window;
		} else {
			this.padding = {x: 0, y: 0};
			this.container = this.element;
		}
		this.overlay = new Element($pick(tag, 'div'), {'styles': {'display': 'none'}}).inject(this.container);
		this.update();
	},

	/*
	Property: show
		Make overlay element visible.
	*/

	show: function(){
		this.overlay.setStyle('display', 'block');
		return this;
	},

	/*
	Property: update
		Recalculate conteiner element scroll size and update overlay element properties.

	Arguments:
		props - optional, see Element properties.
	*/

	update: function(props){
		this.overlay.set($merge(this.options, {'styles': {
			width: this.element.getScrollWidth() - this.padding.x,
			height: this.element.getScrollHeight() - this.padding.y
		}}, props));
		return this;
	},

	/*
	Property: hide
		Make overlay element invisible.
	*/

	hide: function(){
		this.overlay.setStyle('display', 'none');
		return this;
	},

	/*
	Property: destroy
		Destroy overlay element.
	*/

	destroy: function(){
		this.overlay.remove(true);
		return this;
	}

});
Fx.Overlay.implement(new Options);
Fx.Overlay.windowPadding = (window.ie6) ? {x: 21, y: 4} : {x: 0, y: 0};


Element.$overlay = function(hide, deltaZ){
	deltaZ = $pick(deltaZ, 1);
	if (!this.fixOverlayElement) this.fixOverlayElement = new Element('iframe', {
		'properties': {'frameborder': '0', 'scrolling': 'no', 'src': 'javascript:void(0);'},
		'styles': {'position': this.getStyle('position'), 'border': 'none', 'filter': 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'}}).injectBefore(this);
	if (hide) return this.fixOverlayElement.setStyle('display', 'none');
	var z = this.getStyle('z-index').toInt() || 0;
	if (z < deltaZ) this.setStyle('z-index', '' + (z = deltaZ + 1) );
	var pos = this.getCoordinates();
	return this.fixOverlayElement.setStyles({'display' : '', 'z-index': '' + (z - deltaZ),
		'left': pos.left + 'px', 'top': pos.top + 'px',
		'width': pos.width + 'px', 'height': pos.height + 'px'});
};

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/

Element.extend({

	/*
	Property: fixOverlay
		IE only, create or update overlay element to fix 'IE select bug'.
		From digitarald's extended moo. See <http://dev.digitarald.de/js/moo.dev.extend.js>

	Arguments:
		hide - optional, hide overlay element if true.
		deltaZ - optional, (overlay z-index) = (element z-index) - deltaZ. defaults to 1.
	*/

	fixOverlay: window.ie6 ? Element.$overlay : function(){ return false; },

	/*
	Property: remove
		Removes the Element from the DOM. Also removes overlay element if present.

	Arguments:
		trash - if true empties the element and collects it from garbage.
	*/

	remove: function(trash){
		if (this.fixOverlayElement){
			this.fixOverlayElement.remove();
			if (trash){ Garbage.trash([this.fixOverlayElement]); }
		}
		this.parentNode.removeChild(this);
		if (trash){ Garbage.trash([this.empty()]); return false; }
		return this;
	}

});
