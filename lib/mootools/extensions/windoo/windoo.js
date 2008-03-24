//Windoo: Mootools window class <http://code.google.com/p/windoo>. Copyright (c) 2007 Yevgen Gorshkov, MIT Style License.

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
/*
Script: Windoo.Core.js
	Mootools draggable and resizable Window extension.
	Contains <Windoo>, <Windoo.Themes>.

Note:
	Windoo: windows for the cow people (c) ibolmo

TODO:
	- refactor action effects (make effects customizable)
	- manage minimized windows with window manager
	- window popup menu
	- cascade window positioning
	- z-index in container vs wm
	- more themes
*/

/*
Class: Windoo
	Draggable and resizable window class.

Arguments:
	options - The options object.

Options:
	id - optional, window Element id, defaults to unique value;
	title - optional, window title;
	width - required, int, window width in pixels (including window border);
	height - required, int, window height in pixels (including window border);
	position - optional, window position method (one of false, 'center', 'cascade': TODO). if false, top and left options define window coordinates. defaults to 'center';
	top - optional, int, top window coordinate;
	left - optional, int, left window coordinate;
	type - optional, window content type. one of 'dom', 'iframe'. defaults to 'dom';
	url - optional, source URL for 'iframe' and 'ajax' window types to load at start;
	resizable - boolean, defines if the window is resizable. defaults to true;
	draggable - boolean, defines if the window is draggable. defaults to true;
	positionStyle - window element position style value ('absolute', 'fixed'). defaults to 'absolute';
	resizeLimit - optional, window resize limits (see: <Drag.Resize>::limit option);
	destroyOnClose - boolean, if true destroy window instance when close button clicked, otherwise hide window. default to true;
	container - optional, window container element, should have position relative or absolute. defaults to document.body;
	restrict - boolean, if true restrict window dragging and resizing to the container bounds. defaults to true;
	ghost - object, see Ghost below;
	snap - object, see Snap below;
	theme - optional, defines window theme (see: <Windoo.Themes>). defaults to 'windoo';
	shadow - optional, if false turns off window shadow event if such is defined in theme. defaults to true;
	modal - boolean, defines if the window is modal. defaults to false;
	buttons - object, see Buttons below;
	class - opional, additional custom window element class name;
	wm - optional, defines window manager (see: <Windoo.Manager>) to attach window to;
	effects - object, see Effects below;

Ghost:
	resize - boolean, ghost resiaing. defaults to false;
	move - boolean, ghost moving. defaults to false;

Snap:
	resize - see <Drag.Resize> span option;
	move - see <Drag.Move> span option;

Buttons:
	menu - display window control menu button (see Buttons display values below). defaults to false;
	close - display close window control button (see Buttons display values below). defaults to true;
	minimize - display minimize window control button (see Buttons display values below). defaults to true;
	roll - if true minimize button roll window into header. defaults to false;
	maximize - display maximize window control button (see Buttons display values below). defaults to true;

Buttons display values:
	true - display button
	false - do not create buttons
	'disabled' - display inactive button

Effects:
	close - effect object started on window close; see: <Fx.Styles>
	hide - effect object started on window hide; see: <Fx.Styles>
	show - effect object started on window show; see: <Fx.Styles>

Events:
	onFocus - optional, function to execute when window obtains focus;
	onBlur - optional, function to execute when window looses focus;
	onBeforeClose - optional, function to execute before window is closed;
	onClose - optional, function to execute when window is closed;
	onDestroy - optional, function to execute when window is destroyed;
	onHide - optional, function to execute when window is hidden;
	onShow - optional, function to execute when window is shown;
	onMaximize - optional, function to execute when window is maximized;
	onMinimize - optional, function to execute when window is minimized;
	onRestore - optional, function to execute when window state is restored. argument contains the previous window state name;
	onBeforeDrag - optional, function to execute when the user starts to drag window but before initial properties values are calculated;
	onStartDrag - optional, function to execute when the user starts to drag the window;
	onDrag - optional, function to execute at every step of the drag;
	onDragComplete - optional, function to execute when the user completes the drag;
	onBeforeResize - optional, function to execute when the user starts to resize window but before initial properties values are calculated;
	onStartResize - optional, function to execute when the user starts to resize the window;
	onResize - optional, function to execute at every resize step;
	onResizeComplete - optional, function to execute when the user completes the resize;

Example:
	(start code)
	var content = $('win1');
	new Windoo({
		left: 600,
		top: 100,
		title: 'Regular window',
		container: $('container'),
		position: false
	}).adopt(content).show();

	// centered iframe window with ghost drag and resize
	new Windoo({
		width: 640,
		height: 480,
		resizeLimit: {'x':[250], 'y':[200]},
		title: 'IFrame window',
		type: 'iframe',
		container: false,
		ghost: {resize: true, move: true},
		url: 'http://mootools.net'
	}).show();
	(end)
*/

var Windoo = new Class({
	options: {
		type: 'dom',
		url: false,
		title: 'Windoo!',
		width: 300,
		height: 200,
		position: 'center',
		top: 0,
		left: 0,
		resizable: true,
		draggable: true,
		positionStyle: 'absolute',
		resizeLimit: {'x': [0], 'y': [0]},
		padding: {'top': 0, 'right': 0, 'bottom': 0, 'left': 0},
		ghost: {'resize': false, 'move': false},
		snap: {'resize': 6, 'move': 6},
		destroyOnClose: true,
		container: null,
		restrict: true,
		theme: 'alphacube',
		shadow: true,
		modal: false,
		buttons: {
			menu: false,
			close: true,
			minimize: true,
			roll: false,
			maximize: true
		},
		'class': '',
		wm: false,
		effects: {
			show: {
				options: {'duration': 600},
				styles: {'opacity': [0, 1]}
			},
			close: {
				options: {'duration': 600},
				styles: {'opacity': [1, 0]}
			},
			hide: {
				options: {'duration': 600},
				styles: {'opacity': [1, 0]}
			}
		},
		onFocus: Class.empty,
		onBlur: Class.empty,
		onClose: Class.empty,
		onDestroy: Class.empty,
		onHide: Class.empty,
		onShow: Class.empty,
		onMaximize: Class.empty,
		onMinimize: Class.empty,
		onRestore: Class.empty,
		onBeforeDrag: Class.empty,
		onStartDrag: Class.empty,
		onDrag: Class.empty,
		onDragComplete: Class.empty,
		onBeforeResize: Class.empty,
		onStartResize: Class.empty,
		onResize: Class.empty,
		onResizeComplete: Class.empty
	},

	makeResizable: Class.empty,
	makeDraggable: Class.empty,

	initialize: function(options){
		var self = this;
		this.fx = {};
		this.bound = {};
		this.padding = {};
		this.panels = [];
		this.zIndex = 0;
		this.visible = false;

		this.options.id = 'windoo-' + (new Date().getTime());
		this.setOptions(options);
		var theme = this.theme = $type(this.options.theme) == 'string' ? Windoo.Themes[this.options.theme] : this.options.theme;
		this.options.container = $(this.options.container || document.body);
		for (var side in theme.padding) this.padding[side] = theme.padding[side] + this.options.padding[side];

		['x', 'y'].each(function(z){
			var lim = this.options.resizeLimit;
			if ($type(lim[z][0]) == 'number') lim[z][0] = Math.max(lim[z][0], theme.resizeLimit[z][0])
		}, this);

		this.buildDOM()
			.setSize(this.options.width, this.options.height)
			.setTitle(this.options.title)
			.fix();
		if (this.options.position == 'center') this.positionAtCenter();

		this.minimized = false;
		if(this.options.draggable) this.makeDraggable();
		if(this.options.resizable) this.makeResizable();

		this.wm = this.options.wm || Windoo.$wm;
		this.wm.register(this);
		if (this.options.initialize) this.options.initialize.call(this);
	},

	/*
	Property: buildDOM
		internal, construct DOM structure of the window

	Returns:
		The Windoo.
	*/

	buildDOM: function(){
		var theme = this.theme, _p = theme.classPrefix;
		this.el = new Element('div', {
			'id': this.options.id,
			'class': theme.className,
			'styles': {
				'position': this.options.positionStyle,
				'overflow': 'hidden',
				'visibility': 'hidden',
				'top': this.options.top,
				'left': this.options.left
			},
			'events': {
				'mousedown': this.focus.bind(this)
			}
		});

		if (this.options['class']) this.el.addClass(this.options['class']);

		var $row = function(prefix, contentClass){ return '<div class="' + prefix + '-left ' + _p + '-drag"><div class="' + prefix + '-right"><div class="' + contentClass + '"></div></div></div>'; };
		var iefix = window.ie && this.options.type != 'iframe',
			innerContent = '<div class="' + _p + '-frame">' + $row("top", "title") + $row("bot", "strut") + '</div><div class="' + _p + '-body">' + (iefix ? Windoo.ieTableCell : '') + '</div>';
		this.el.setHTML(innerContent).inject(this.options.container);
		if (window.ie) this.el.addClass(_p + '-' + theme.name + '-ie');

		var frame = this.el.getFirst(),
			body = this.el.getLast(),
			titleBody = frame.getElement('.title'),
			titleText = new Element('div', {'class': 'title-text'}).inject(titleBody);
		this.dom = {
			frame: frame,
			body: body,
			title: titleText,
			titleBody: titleBody,
			strut: frame.getElement('.strut').setHTML('&nbsp;'),
			content: iefix ? body.getElement('td') : body
		};

		if (this.options.type == 'iframe'){
			this.dom.iframe = new Element('iframe', {
				'frameborder': '0',
				'class': _p + '-body',
				'styles': {'width': '100%', 'height': '100%'}
			});
			this.dom.body.setStyle('overflow', 'hidden');
			this.adopt(this.dom.iframe).setURL(this.options.url);
		}
		return this.buildShadow().buildButtons();
	},

	/*
	Property: buildButtons
		internal, construct DOM structure of the window buttons

	Returns:
		The Windoo.
	*/

	buildButtons: function(){
		var self = this, buttons = this.options.buttons, _p = this.theme.classPrefix;
		var action = function(name, bind){ return function(ev){ new Event(ev).stop(); (bind[name])(); }; };
		this.bound.noaction = function(ev){ new Event(ev).stop(); };
		var makeButton = function(opt, name, title, action){
			self.bound[name] = action;
			if (opt){
				var klass = _p + '-button ' + _p + '-' + name + ( opt == 'disabled' ? ' ' + _p + '-' + name + '-disabled' : '' );
				self.dom[name] = new Element('a', {'class': klass, 'href': '#', 'title': title}).setHTML('x').inject(self.el);
				self.dom[name].addEvent('click', opt == 'disabled' ? self.bound.noaction : action);
			}
		};
		makeButton(buttons.close, 'close', 'Close', action('close', this));
		makeButton(buttons.maximize, 'maximize', 'Maximize', action('maximize', this));
		if (buttons.maximize == true) this.dom.titleBody.addEvent('dblclick', this.maximize.bind(this));
		makeButton(buttons.minimize, 'minimize', 'Minimize', action(buttons.roll ? 'roll' : 'minimize', this));
		makeButton(buttons.minimize, 'restore', 'Restore', action('minimize', this));
		makeButton(buttons.menu, 'menu', 'Menu', action('openmenu', this));
		return this;
	},

	/*
	Property: buildShadow
		internal, construct window shadow element

	Returns:
		The Windoo.
	*/

	buildShadow: function(){
		var theme = this.theme;
		if (this.options.modal) this.modalOverlay = new Fx.Overlay(this.el.getParent(), {'class': this.classPrefix('modal-overlay')});
		if (!theme.shadow || !this.options.shadow) return this;
		this.shadow = new Element('div', {
			'styles': {
				'position': this.options.positionStyle,
				'display': 'none'
			},
			'class': theme.classPrefix + '-shadow-' + theme.shadow
		}).injectAfter(this.el);
		if (theme.complexShadow){
			var $row = function(name){
				var els = ['l', 'r', 'm'].map(function(e){ return new Element('div', {'class': e}); });
				var el = new Element('div', {'class': name});
				return el.adopt.apply(el, els);
			};
			this.shadow.adopt($row('top'), this.dom.shm = $row('mid'), $row('bot'));
		} else {
			this.shadow.adopt(new Element('div', {'class': 'c'}));
		}
		return this;
	},

	/*
	Property: setHTML
		Set window content to a string; should not be used with 'iframe' window type.

	Arguments:
		content - the HTML content string

	Returns:
		The Windoo.
	*/

	setHTML: function(content){
		if (!this.dom.iframe) this.dom.content.empty().setHTML(content);
		return this;
	},

	/*
	Property: adopt
		Inserts the passed element(s) inside the Windoo; should not be used with 'iframe' window type.

	Arguments:
		el - an element reference or the id of the element to be injected inside

	Returns:
		The Windoo.
	*/

	adopt: function(){
		this.dom.content.empty().adopt.apply(this.dom.content, arguments);
		return this;
	},

	/*
	Property: wrap
		Wrap the element into a window inheriting original element size.

	Arguments:
		el - an element reference or the id of the element to be injected inside window.
		options - the Options object (see below).

	Options:
		position - optional, if true, move window at $(el) position.
		ignorePadding - optional, if true all window theme paddings are set to 0, defaults to false.

	Returns:
		The Windoo.
	*/

	wrap: function(el, options){
		var styles = {'margin': '0', 'position': 'static'};
		el = $(el);
		options = options || {};
		var size = el.getSize().size, pos = el.getPosition(), coeff = options.ignorePadding ? 0 : 1, pad = this.padding;
		this.setSize(size.x + coeff * (pad.right + pad.left), size.y + coeff * (pad.top + pad.bottom));
		if (options.resetWidth) styles.width = 'auto';
		if (options.position) this.setPosition(pos.x - coeff * pad.left, pos.y - coeff * pad.top);
		this.dom.content.empty().adopt(el.remove().setStyles(styles));
		return this;
	},

	/*
	Property: empty
		Empties window content or set iframe source to 'about:blank' page.

	Returns:
		The Windoo.
	*/

	empty: function(){
		if (this.dom.iframe) this.dom.iframe.src = 'about:blank';
		else this.dom.content.empty();
		return this;
	},

	/*
	Property: setURL
		Set URL to load into the window if window type is 'iframe'.

	Arguments:
		url - the url string to load

	Returns:
		The Windoo.
	*/

	setURL: function(url){
		if (this.dom.iframe) this.dom.iframe.src = url || 'about:blank';
		return this;
	},

	/*
	Property: getContent
		Returns window content element.
	*/

	getContent: function(){
		return this.dom.content;
	},

	/*
	Property: setTitle
		Set window title.

	Arguments:
		title - the title string

	Returns:
		The Windoo.
	*/

	setTitle: function(title){
		this.dom.title.setHTML(title || '&nbsp;');
		return this;
	},

	/*
	Property: effect
		Perform an action with registered action effect.

	Arguments:
		name - name of an action and effect
		noeffect - if false, perform action without effect
		onComplete - function to execute when the effect is finished

	Returns:
		The Windoo.
	*/

	effect: function(name, noeffect, onComplete){
		opts = {onComplete: onComplete};
		if (noeffect) opts.duration = 0;
		var fx = this.options.effects[name];
		new Fx.Styles(fx.el || this.el, $merge(fx.options, opts)).start(fx.styles);
		if (this.shadow) new Fx.Styles(this.shadow, fx.options).start(fx.styles);
		return this;
	},

	/*
	Property: hide
		Hide window.

	Arguments:
		noeffect - optional, if true, hide window immediately without effect

	Returns:
		The Windoo.
	*/

	hide: function(noeffect){
		if (!this.visible) return this;
		this.visible = false;
		return this.effect('hide', noeffect, function(){
			this.el.setStyle('display', 'none');
			if (this.modalOverlay) this.modalOverlay.hide();
			this.fix(true).fireEvent('onHide');
		}.bind(this));
	},

	/*
	Property: show
		Show window.

	Arguments:
		noeffect - optional, if true, show window immediately without effect

	Returns:
		The Windoo.
	*/

	show: function(noeffect){
		if (this.visible) return this;
		this.visible = true;
		if (this.modalOverlay) this.modalOverlay.show();
		this.el.setStyle('display', '');
		this.bringTop().fix();
		if (this.shadow) this.shadow.setStyle('visibility', 'hidden');
		return this.effect('show', noeffect, function(){
			this.el.setStyle('visibility', 'visible');
			this.fireEvent('onShow').fix();
		}.bind(this));
	},

	/*
	Property: fix
		internal, update window overlay and shadow.

	Returns:
		The Windoo.
	*/

	fix: function(hide){
		this.el.fixOverlay(hide || !this.visible);
		return this.fixShadow(hide);
	},

	/*
	Property: fixShadow
		internal, update shadow position and visibility according to the current window state.

	Returns:
		The Windoo.
	*/

	fixShadow: function(hide){
		if (this.shadow){
			this.shadow[(this.maximized ? 'add' : 'remove') + 'Class']('windoo-shadow-' + this.theme.name + '-maximized');
			if (hide || !this.visible){
				this.shadow.setStyle('display', 'none');
			} else {
				var pos = this.el.getCoordinates(), pad = this.theme.shadowDisplace;
				this.shadow.setStyles({'display': '', 'zIndex': this.zIndex - 1,
					'left': this.el.offsetLeft + pad.left, 'top': this.el.offsetTop + pad.top,
					'width': pos.width + pad.width, 'height': pos.height + pad.height});
				if (this.dom.shm) this.dom.shm.setStyle('height', pos.height - pad.delta);
			}
		}
		return this;
	},

	/*
	Property: getState
		Returns current window State.

	State:
		outer - outer border coordinates;
		inner - window content size
	*/

	getState: function(){
		var outer = this.el.getCoordinates(), container = this.options.container,
			cont = container === $(document.body) ? {'top': 0, 'left': 0} : container.getCoordinates();
		outer.top -= cont.top;
		outer.right -= cont.left;
		outer.bottom -= cont.top;
		outer.left -= cont.left;
		return {outer: outer, inner: this.dom.content.getSize()};
	},

	/*
	Property: setState
		Set window size (outer border size).
	
	Arguments:
		width - int, window width in pixels
		height - int, window height in pixels

	Returns:
		The Windoo.
	*/

	setSize: function(width, height){
		var pad = this.padding;
		this.el.setStyles({'width': width, 'height': height});
		this.dom.strut.setStyle('height', Math.max(0, height - pad.top));
		this.dom.body.setStyle('height', Math.max(0, height - pad.top - pad.bottom));
		return this.fix().fireEvent('onResizeComplete', this.fx.resize);
	},

	/*
	Property: positionAtCenter
		Make window positioned at the browser window center.
	
	Arguments:
		offset - optional, window coordinates Offset object (see Offset below)

	Offset:
		x - int, horizontal offset
		y - int, vertical offset

	Returns:
		The Windoo.
	*/

	positionAtCenter: function(offset){
		offset = $merge({'x': 0, 'y': 0}, offset);
		var container = this.options.container;
		if (container === document.body) container = window;
		var s = container.getSize(), esize = this.el.getSize().size,
			fn = function(z){ return Math.max(0, offset[z] + s.scroll[z] + (s.size[z] - esize[z])/2); };
		this.el.setStyles({'left': fn('x'), 'top': fn('y')});
		return this.fix();
	},

	/*
	Property: setPosition
		Set window position.
	
	Arguments:
		x - int, horizontal coordinate in pixels;
		y - int, certical coordinate in pixels;

	Returns:
		The Windoo.
	*/

	setPosition: function(x, y){
		this.el.setStyles({'left': x, 'top': y});
		return this.fix();
	},

	/*
	Property: preventClose
		Prevent closing the window. Should be called from inside the onClose event handler.

	Arguments:
		prevent - if defined, override the default value; defaults to true.
	*/

	preventClose: function(prevent){
		this.$preventClose = $defined(prevent) ? prevent : true;
		return this;
	},

	/*
	Property: close
		Close window and destroy if destroyOnClose option is set.

	Arguments:
		noeffect - optional, if true, close window immediately without effect
	*/

	close: function(noeffect){
		this.$preventClose = false;
		this.fireEvent('onBeforeClose');
		if (this.$preventClose) return this;
		if (!this.visible) return this;
		this.visible = false;
		return this.effect('close', noeffect, function(){
			this.el.setStyle('display', 'none');
			if (this.modalOverlay) this.modalOverlay.hide();
			this.fix(true).fireEvent('onClose');
			if (this.options.destroyOnClose) this.destroy();
		}.bind(this));
	},

	/*
	Property: destroy
		Destoroy window immediately.
	*/

	destroy: function(){
		this.fireEvent('onDestroy');
		this.wm.unregister(this);
		if (this.modalOverlay) this.modalOverlay.destroy();
		if (this.shadow) this.shadow.remove(true);
		this.el.remove(true);
		for (var z in this) this[z] = null;
		this.destroyed = true;
	},

	classPrefix: function(klass){
		return [this.theme.classPrefix, this.theme.name, klass + ' ' + this.theme.classPrefix, klass].join('-');
	},

	/*
	Property: maximize
		Toggle maximized window state.

	Arguments:
		noeffect - TODO, optional, if true, toggle window state immediately without effect

	Returns:
		The Windoo.
	*/

	maximize: function(noeffect){
		if (this.minimized) return this.minimize();
		if (this.rolled) this.roll(true);
		var bound = function(value, limit){
			if (!limit) return value;
			if (value < limit[0]) return limit[0];
			if (limit.length > 1 && value > limit[1]) return limit[1];
			return value;
		};
		var klass = this.classPrefix('maximized');
		this.maximized = !this.maximized;
		this.minimized = false;
		if (this.maximized){
			this.$restoreMaxi = this.getState();
			var container = this.options.container;
			if (container === document.body) container = window;
			var s = container.getSize(), limit = this.options.resizeLimit;
			if (limit) for (var z in limit) s.size[z] = bound(s.size[z], limit[z]);
			this.el.addClass(klass);
			this.setSize(s.size.x, s.size.y)
				.setPosition(s.scroll.x, s.scroll.y)
				.fireEvent('onMaximize');
		} else {
			this.el.removeClass(klass);
			this.restoreState(this.$restoreMaxi).fireEvent('onRestore', 'maximize');
		}
		return this.fix();
	},

	/*
	Property: minimize
		Toggle minimized window state (FIXME: add WM layout for minimized windows).

	Arguments:
		noeffect - optional, if true, toggle window state immediately without effect

	Returns:
		The Windoo.
	*/

	minimize: function(noeffect){
		var klass = this.classPrefix('minimized');
		this.minimized = !this.minimized;
		if (this.minimized){
			this.$restoreMini = this.getState();
			var container = this.options.container;
			if (container === document.body) container = window;
			var s = container.getSize(), height = this.theme.padding.top + this.theme.padding.bottom;
			this.el.addClass(klass);
			this.setSize('auto', height)
				.setPosition(s.scroll.x + 10, s.scroll.y + s.size.y - height - 10)
				.fireEvent('onMinimize');
		} else {
			this.el.removeClass(klass);
			this.restoreState(this.$restoreMini).fireEvent('onRestore', 'minimize');
		}
		return this.fix();
	},

	restoreState: function(state){
		state = state.outer;
		return this.setSize(state.width, state.height).setPosition(state.left, state.top);
	},

	/*
	Property: roll

	Arguments:
		noeffect - optional, if true, toggle window state immediately without effect

	Returns:
		The Windoo.
	*/

	roll: function(noeffect){
		var klass = this.classPrefix('rolled');
		this.rolled = !this.rolled;
		if (this.rolled){
			this.$restoreRoll = this.getState().outer;
			var pad = this.theme.padding;
			this.setSize(this.$restoreRoll.width, pad.top + pad.bottom);
			this.el.addClass(klass);
			this.fireEvent('onRoll');
		} else {
			this.el.removeClass(klass);
			var state = this.$restoreRoll;
			this.setSize(state.width, state.height).fireEvent('onRestore', 'roll');
		}
		return this.fix();
	},

	/*
	Property: openmenu
		TODO, toggle window popup menu.

	Returns:
		The Windoo.
	*/

	openmenu: function(){
		this.fireEvent('onMenu');
		return this;
	},

	/*
	Property: setZIndex
		internal, set window z-index.

	Arguments:
		z - z-index value

	Returns:
		The Windoo.
	*/

	setZIndex: function(z){
		this.zIndex = z;
		this.el.setStyle('zIndex', z);
		if (this.el.fixOverlayElement) this.el.fixOverlayElement.setStyle('zIndex', z - 1);
		if (this.shadow) this.shadow.setStyle('zIndex', z - 1);
		if (this.fx.resize) this.fx.resize.options.zIndex = z + 1;
		if (this.modalOverlay) this.modalOverlay.overlay.setStyle('zIndex', z - 2);
		return this;
	},

	/*
	Property: focus
		Bring focus to the window.

	Returns:
		The Windoo.
	*/

	focus: function(){
		this.el.removeClass(this.theme.classPrefix + '-blur');
		if (this.wm.focus(this)) this.fireEvent('onFocus');
		return this;
	},

	/*
	Property: blur
		Remove focus from the window if focused.

	Returns:
		The Windoo.
	*/

	blur: function(){
		this.el.addClass(this.theme.classPrefix + '-blur');
		if (this.wm.blur(this)) this.fireEvent('onBlur');
		return this;
	},

	/*
	Property: bringTop
		Put window on top of the others.

	Returns:
		The Windoo.
	*/

	bringTop: function(){
		return this.setZIndex(this.wm.maxZIndex());
	}
});
Windoo.implement(new Events, new Options);
Windoo.ieTableCell = '<table style="position:absolute;top:0;left:0;border:none;border-collapse:collapse;padding:0;"><tr><td style="border:none;overflow:auto;position:relative;padding:0;"></td></tr></table>';

/*
Class: Windoo.Themes
	Window themes descriptions.
*/

Windoo.Themes = {

	/*
	Property: cssFirefoxMac
		Firefox/Mac-specific CSS; fixes overlapping scrollbars bug for Windoo
	*/

	cssFirefoxMac: '.windoo-blur * {overflow: hidden !important;}',

	/*
	Property: alphacube
		Modified Alphacube-color theme; For the original theme see: <http://art.gnome.org/themes/metacity/1171>
	*/

	alphacube: {
		'name': 'alphacube',
		'padding': {'top': 22, 'right': 10, 'bottom': 15, 'left': 10},
		'resizeLimit': {'x': [275], 'y': [37]},
		'className': 'windoo windoo-alphacube',
		'sizerClass': 'sizer',
		'classPrefix': 'windoo',
		'ghostClass': 'windoo-ghost windoo-alphacube-ghost windoo-hover',
		'hoverClass': 'windoo-hover',
		'shadow': 'simple window-shadow-alphacube-simple',
		'shadeBackground': 'transparent url(windoo/s.gif)',
		'shadowDisplace': {'left': 3, 'top': 3, 'width': 0, 'height': 0}
	}
};

if (window.gecko && navigator.appVersion.indexOf('acintosh') >= 0) window.addEvent('domready', function(){ new Element('style', {'type': 'text/css', 'media': 'all'}).inject(document.head).appendText(Windoo.Themes.cssFirefoxMac); });
/*
Script: Windoo.Manager.js
	Windoo window manager.
	Contains <Windoo.Manager>, <Windoo.$wm>.
*/

/*
Class: Windoo.Manager
	Window manager class.

Options:
	zIndex - Starting window z-index value;
	onRegister - optional, function to execute when window is registered;
	onUnregister - optional, function to execute when window is unregistered;
	onFocus - optional, function to execute when window is focused;
	onBlur - optional, function to execute when window loses focus;
*/

Windoo.Manager = new Class({
	focused: false,
	options: {
		zIndex: 100,
		onRegister: Class.empty,
		onUnregister: Class.empty,
		onFocus: Class.empty,
		onBlur: Class.empty
	},

	initialize: function(options){
		this.hash = [];
		this.setOptions(options);
	},

	/*
	Property: maxZIndex
		Returns maximal z-index value of all windows.
	*/

	maxZIndex: function(){
		var windows = this.hash;
		if (!windows.length) return this.options.zIndex;
		var zindex = [];
		windows.each(function(item){ this.push(item.zIndex);}, zindex);
		zindex.sort(function(a, b){ return a - b; });
		return zindex.getLast() + 3;
	},

	/*
	Property: register
		internal, register new window in the manager.
	*/

	register: function(win){
		win.setZIndex(this.maxZIndex());
		this.hash.push(win);
		return this.fireEvent('onRegister', win);
	},

	/*
	Property: unregister
		internal, unregister window.
	*/

	unregister: function(win){
		this.hash.remove(win);
		if (this.focused === win) this.focused = false;
		return this.fireEvent('onUnregister', win);
	},

	/*
	Property: focus
		internal, set focus to the window.

	Arguments:
		win - window to set as focused
	*/

	focus: function(win){
		if (win === this.focused) return false;
		if (this.focused) this.focused.blur();
		this.focused = win;
		win.bringTop(this.maxZIndex());
		return this.fireEvent('onFocus', win);
	},

	/*
	Property: blur
		internal, remove focus from the window if focused. Returns true if focus is removed.

	Arguments:
		win - window to remove focus from
	*/

	blur: function(win){
		if (this.focused === win){
			this.focused = false;
			this.fireEvent('onBlur', win);
			return true;
		}
		return false;
	}

});
Windoo.Manager.implement(new Events, new Options);

/*
Property: Windoo.$wm
	Default window manager object.
*/

Windoo.$wm = new Windoo.Manager();
/*
Script: Windoo.Drag.js
	Draggable and resizable effects for Windoo class.
	Contains <Windoo::makeDraggable>, <Windoo::makeResizable>.
*/

/*
Class: Windoo
	Draggable and resizable window class.
*/

Windoo.implement({

	/*
	Property: makeResizable
		internal, add resizable effect to the window (see: <Drag.Resize>)
	*/

	makeResizable: function(){
		var self = this, theme = this.theme, opt = this.options, inbody = opt.container === $(document.body);
		this.fx.resize = this.el.makeResizable({
			ghostClass: theme.ghostClass,
			hoverClass: theme.hoverClass,
			classPrefix: theme.classPrefix + '-sizer ' + theme.classPrefix + '-',
			shadeBackground: theme.shadeBackground,

			container: (opt.restrict && !inbody) ? opt.container : false,
			resizeLimit: opt.resizeLimit,
			ghost: opt.ghost.resize,
			snap: opt.snap.resize,

			onBeforeStart: function(){
				self.fireEvent('onBeforeResize', this).focus();
			},
			onStart: function(fx){
				if (self.maximized){
					fx.stop();
				} else {
					if (!this.ghost && window.gecko) Element.$overlay.call(fx.shade.overlay);
					self.fireEvent('onStartResize', this);
				}
			},
			onResize: function(){
				self.fireEvent('onResize', this);
			},
			onComplete: function(){
				if (this.ghost){
					var size = self.getState().outer;
					self.setSize(size.width, size.height);
				} else {
					self.fix().fireEvent('onResizeComplete', this);
				}
			},
			onBuild: function(dir, binds){
				if (!this.ghost){
					var fx = this.fx[dir], nolimit = {'x':{'limit': false}, 'y':{'limit': false}};
					if (binds.resize.y) ['strut', 'body', 'shm'].each(function(name){
						if (this[name]) fx.add(this[name], {'y': {direction: binds.resize.y.direction, style: 'height'}}, binds.resize);
					}, self.dom);
					[self.shadow, self.el.fixOverlayElement].each(function(el){
						if (el){
							fx.add(el, $merge(binds.resize, nolimit), binds.resize);
							if (binds.move) fx.add(el, $merge(binds.move, nolimit), binds.move);
						}
					}, self);
				}
			}
		});
	},

	/*
	Property: makeDraggable
		internal, add drag effect to the window (see: <Drag.Move>)
	*/

	makeDraggable: function(){
		var self = this, fx = this.fx.drag = [], inbody = this.options.container === $(document.body);
		var xLimit = function(){ return 2 - self.el.offsetWidth; };
		var opts = {
			container: (this.options.restrict && !inbody ? this.options.container : null),
			limit: (inbody ? {'x': [xLimit], 'y': [0]} : {}),
			snap: this.options.snap.move,
			onBeforeStart: function(){
				self.focus();
				this.shade = new Fx.Overlay(window, {'styles': {
					'cursor': this.options.handle.getStyle('cursor'),
					'background': self.theme.shadeBackground,
					'zIndex': self.zIndex + 3
				}}).show();
				if (self.ghost){
					var ce = self.el.getSize().size;
					this.element.setStyles({
						'zIndex': self.zIndex + 3,
						'left': self.el.getStyle('left'),
						'top': self.el.getStyle('top'),
						'width': ce.x,
						'height': ce.y
					});
				} else if (window.gecko){
					Element.$overlay.call(this.shade.overlay, false, 2);
				}
				self.fireEvent('onBeforeDrag', this);
			},
			onStart: function(){
				if (self.maximized && !self.minimized) this.stop();
				else self.fireEvent('onStartDrag', this);
			},
			onSnap: function(){
				if (self.ghost) this.element.setStyle('display', 'block');
			},
			onDrag: function(){
				self.fix().fireEvent('onDrag', this);
			},
			onComplete: function(){
				this.shade.destroy();
				if (self.ghost){
					for (var z in this.options.modifiers){
						var style = this.options.modifiers[z];
						self.el.setStyle(style, this.element.getStyle(style));
					}
					this.element.setStyle('display', 'none');
				}
				self.fix().fireEvent('onDragComplete', this);
			}
		};
		if (this.options.ghost.move) this.ghost = new Element('div', {'class': this.theme.ghostClass, 'styles': {'display': 'none'}}).injectAfter(this.el);
		this.el.getElements('.' + this.theme.classPrefix + '-drag').each(function(d){
			opts.handle = d;
			d.setStyle('cursor', 'move');
			fx.push((this.ghost || this.el).makeDraggable(opts));
		}, this);
	}

});
/*
Script: Windoo.Dialog.js
	Windoo standard modal dialog utility classes.
	Contains <Windoo.Alert>, <Windoo.Confirm>.
*/

/*
Property: Windoo.Dialog
	Abstract inline modal Dialog class.

Arguments:
	message - message text.
	options - Options object.

Options:
	window - custom Windoo window options. see Windoo options.
	buttons - Buttons object.
	panel - custom Panel element options. see Element options.
	message - custom message element options. see Element options.

Buttons:
	ok - custom OK button options. see Element options.
	cancel - custom Cancel button options. see Element options.

Events:
	onConfirm - optional, function to execute when dialog is confirmed.
	onCancel - optional, function to execute when dialog is rejected.

*/

Windoo.Dialog = Windoo.extend({

	initialize: function(message, options){
		var self = this, dialog = this.dialog = {
			dom: {},
			buttons: {},
			options: $merge(Windoo.Dialog.options, options),
			message: message
		};
		this.parent($merge({
			'onShow': function(){
				if (dialog.buttons.ok) dialog.buttons.ok.focus();
			}
		}, dialog.options.window));
		dialog.bound = function(ev){
			ev = new Event(ev);
			if (['enter', 'esc'].contains(ev.key)){
				dialog.result = (ev.key == 'enter') ? !dialog.cancelFocused : false;
				self.close();
				ev.stop();
			}
		};
		document.addEvent('keydown', dialog.bound);
		this.addEvent('onClose', function(){
			document.removeEvent('keydown', dialog.bound);
			dialog.options[(dialog.result) ? 'onConfirm' : 'onCancel'].call(this);
		});
	},

	buildDialog: function(klass, buttons){
		var self = this, dialog = this.dialog;
		if ('ok' in buttons) dialog.buttons.ok =  new Element('input', $merge({
			'events': {
				'click': function(){
					dialog.result = true;
					self.close();
				}
			}
		}, dialog.options.buttons.ok));
		if ('cancel' in buttons) dialog.buttons.cancel = new Element('input', $merge({
			'events': {
				'click': function(){
					dialog.result = false;
					self.close();
				}
			}
		}, dialog.options.buttons.cancel)).addEvents({
			'focus': function(){
				dialog.cancelFocused = true;
			},
			'blur': function(){
				dialog.cancelFocused = false;
			}
		});
		dialog.dom.panel = new Element('div', $merge({'class': this.classPrefix(klass + '-pane')}, dialog.options.panel));
		for (var btn in buttons) if (buttons[btn]) dialog.dom.panel.adopt(dialog.buttons[btn]);
		dialog.dom.message = new Element('div', $merge({'class': this.classPrefix(klass + '-message')}, dialog.options.message));
		return this.addPanel(dialog.dom.panel).adopt(dialog.dom.message.setHTML(dialog.message));
	}

});

Windoo.Dialog.options = {
	'window': {
		'modal': true,
		'resizable': false,
		'buttons': {
			'minimize': false,
			'maximize': false
		}
	},
	'buttons': {
		'ok': {
			'properties': {
				'type': 'button',
				'value': 'OK'
			}
		},
		'cancel': {
			'properties': {
				'type': 'button',
				'value': 'Cancel'
			}
		}
	},
	'panel': null,
	'message': null,
	'onConfirm': Class.empty,
	'onCancel': Class.empty
};

/*
Property: Windoo.Alert
	Alert inline dialog class.
	Inherits properties, methods, events, and options from <Windoo.Dialog>.

Arguments:
	message - message text.
	options - Windoo.Dialog options object.
*/

Windoo.Alert = Windoo.Dialog.extend({

	initialize: function(message,  options){
		this.parent(message,  options);
		this.buildDialog('alert', {'ok': true}).show();
	}

});

/*
Property: Windoo.Confirm
	Confirm inline dialog class.
	Inherits properties, methods, events, and options from <Windoo.Dialog>.

Arguments:
	message - message text.
	options - Windoo.Dialog options object.

*/

Windoo.Confirm = Windoo.Dialog.extend({

	initialize: function(message,  options){
		this.parent(message,  options);
		this.buildDialog('confirm', {'ok': true, 'cancel': true}).show();
	}

});
/*
Script: Windoo.Panel.js
	Windoo extension for creating border panels (toolbars).
	Contains <Windoo::addPanel>, <Windoo::removePanel>.
*/

Windoo.implement({

	/*
	Property: addPanel
		Add border panel to the window at specified position and recalculate window paddings.

	Arguments:
		element - the panel content $(element)
		position - one of 'bottom', 'top', 'left', 'right'

	Returns:
		The Windoo.
	*/

	addPanel: function(element, position){
		position = $pick(position, 'bottom');
		var dim, ndim,
			size = this.el.getSize().size,
			styles = {'position': 'absolute'},
			panel = {'element': $(element), 'position': position, 'fx': []};
		switch (position){
			case 'top':
			case 'bottom': dim = 'x'; ndim = 'y'; break;
			case 'left':
			case 'right': dim = 'y'; ndim = 'x'; break;
			default: return this;
		}
		var options = Windoo.panelOptions[dim];
		styles[position] = this.padding[position];
		styles[options.deltaP] = this.padding[options.deltaP];
		element = panel.element.addClass(this.classPrefix('pane')).setStyles(styles).inject(this.el);
		panel.padding = element.getSize().size[ndim];
		this.padding[position] += panel.padding;
		if (this.options.resizable && !this.options.ghost.resize){
			this.fx.resize.add(function(dir, binds){
				if (binds.resize[dim]){
					var fx = this.fx[dir], mod = {};
					mod[dim] = $merge(binds.resize[dim]);
					mod[dim].limit = null;
					panel.fx.push({
						'fx': fx,
						'bind': fx.add(panel.element, mod, binds.resize)
					});
				}
			});
		}
		this.addEvent('onResizeComplete', function(){
			panel.element.setStyle(options.style, this.el.getSize().size[dim] - this.padding[options.deltaM] - this.padding[options.deltaP] - 1);
		});
		this.panels.push(panel);
		return this.setSize(size.x, size.y);
	},

	/*
	Property: removePanel
		Remove window border panel.

	Arguments:
		element - the panel content $(element)

	Returns:
		The Windoo.
	*/

	removePanel: function(element){
		var panel, size;
		element = $(element);
		for (var i = 0, len = this.panels.length; i < len; i++){
			panel = this.panels[i];
			if (panel.element === element){
				this.padding[panel.position] -= panel.padding;
				panel.element.remove();
				panel.fx.each(function(pfx){ pfx.fx.detach(pfx.bind); }, this);
				this.panels.splice(i, 1);
				size = this.el.getSize().size;
				this.setSize(size.x, size.y);
				break;
			}
		}
		return this;
	}

});

Windoo.panelOptions = {
	'x': {'style': 'width', 'deltaP': 'left', 'deltaM': 'right'},
	'y': {'style': 'height', 'deltaP': 'top', 'deltaM': 'bottom'}
};
/*
Script: Windoo.Ajax.js
	Ajax class extension for updating Windoo instance content.
	Contains <Windoo.Ajax>.
*/

/*
Class: Windoo.Ajax
	Extended <Ajax> class to update window content.

Options:
	window - Windoo object to insert the response text of the XHR into, upon completion of the request.
*/

Windoo.Ajax = Ajax.extend({
	onComplete: function(){
		if (this.options.window) this.options.window.setHTML(this.response.text);
		this.parent();
	}
});

/*
Script: Windoo.Themes.js
	Windoo additional themes.
	Contains <Windoo.Themes.aero>.
*/

/*
Property: Windoo.Themes.aero
	Modified 'aero' theme from YUI-Ext library <http://extjs.com/>
*/

Windoo.Themes.aero = {
	'name': 'aero',
	'padding': {'top': 28, 'right': 10, 'bottom': 15, 'left': 10},
	'resizeLimit': {'x': [175], 'y': [58]},
	'className': 'windoo windoo-aero',
	'sizerClass': 'sizer',
	'classPrefix': 'windoo',
	'ghostClass': 'windoo-ghost windoo-aero-ghost windoo-hover',
	'hoverClass': 'windoo-hover',
	'shadow': 'simple window-shadow-aero-simple',
	'shadeBackground': 'transparent url(windoo/s.gif)',
	'shadowDisplace': {'left': 3, 'top': 3, 'width': 0, 'height': 0}
};

/*
Property: Windoo.Themes.aqua
	MacOS X aqua theme
*/

Windoo.Themes.aqua = {
	'name': 'aqua',
	'padding': {'top': 23, 'right': 0, 'bottom': 15, 'left': 0},
	'resizeLimit': {'x': [275], 'y': [37]},
	'className': 'windoo windoo-aqua',
	'sizerClass': 'sizer',
	'classPrefix': 'windoo',
	'ghostClass': 'windoo-ghost windoo-aqua-ghost windoo-hover',
	'hoverClass': 'windoo-hover',
	'shadeBackground': 'transparent url(themes/windoo/s.gif)',
	'shadow': 'aqua',
	'complexShadow': true,
	'shadowDisplace': {'left': -13, 'top': -8, 'width': 26, 'height': 31, 'delta': 23}
};

/*
Property: Windoo.Themes.nada
	Simple nada theme
*/

Windoo.Themes.nada = {
	'name': 'nada',
	'padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0},
	'resizeLimit': {'x': [0], 'y': [0]},
	'className': 'windoo windoo-nada',
	'sizerClass': 'sizer',
	'classPrefix': 'windoo',
	'ghostClass': 'windoo-ghost windoo-nada-ghost windoo-hover',
	'hoverClass': 'windoo-hover',
	'shadeBackground': 'transparent',
	'shadow': 'nada',
	'complexShadow': true,
	'shadowDisplace': {'left': 0, 'top': 0, 'width': 0, 'height': 0, 'delta': 0}
};

/*
Property: Windoo.Themes.mypage
	Modified 'mypage' theme from 'aero' theme
*/

Windoo.Themes.mypage = {
	'name': 'mypage',
	'padding': {'top': 16, 'right': 4, 'bottom': 4, 'left': 4},
	'resizeLimit': {'x': [175], 'y': [58]},
	'className': 'windoo windoo-mypage',
	'sizerClass': 'sizer',
	'classPrefix': 'windoo',
	'ghostClass': 'windoo-ghost windoo-mypage-ghost windoo-hover',
	'hoverClass': 'windoo-hover',
	'shadow': 'simple window-shadow-mypage-simple',
	'shadeBackground': 'transparent url(windoo/s.gif)',
	'shadowDisplace': {'left': 2, 'top': 2, 'width': 0, 'height': 0}
};

/*
Property: Windoo.Themes.mypage_view
	Modified 'mypage_view' theme from 'mypage' theme
*/

Windoo.Themes.mypage_view = {
	'name': 'mypage_view',
	'padding': {'top': 16, 'right': 4, 'bottom': 4, 'left': 4},
	'resizeLimit': {'x': [175], 'y': [58]},
	'className': 'windoo windoo-mypage_view',
	'sizerClass': 'sizer',
	'classPrefix': 'windoo',
	'ghostClass': 'windoo-ghost windoo-mypage_view-ghost windoo-hover',
	'hoverClass': 'windoo-hover',
	'shadow': 'simple window-shadow-mypage_view-simple',
	'shadeBackground': 'transparent url(windoo/s.gif)',
	'shadowDisplace': {'left': 0, 'top': 0, 'width': 0, 'height': 0}
};
/*
Script: Drag.Multi.js
	Mootools Drag.Base class extension which adds support for modifying multiple css properties of different elements simultaneously.
	Contains <Drag.Multi>.

License:
	MIT-style license.

Copyright:
	copyright (c) 2007 Yevgen Gorshkov
*/

// internal, the default Drag.Transition linear function and it's inverse

Drag.Transition = {
	linear:{
		step: function(start, current, direction){
			return direction * current - start;
		},
		inverse: function(start, current, direction){
			return (start + current) / direction;
		}
	}
};

/*
Class: Drag.Multi
	Modify multiple css properties of multiple elements based on the position of the mouse.

Arguments:
	options - The options object.

Options:
	handle - required, the $(element) to act as the handle for the draggable elements.
	onStart - optional, function to execute when the user starts to drag (on mousedown);
	onBeforeStart - optional, function to execute when the user starts to drag (on mousedown) but before initial properties values are calculated;
	onComplete - optional, function to execute when the user completes the drag.
	onSnap - optional, function to execute when the distance between staring point and current mouse position exceeds snap option value
	onDrag - optional, function to execute at every step of the drag
	snap - optional, the distance you have to drag before the element starts to respond to the drag. defaults to false

Example:
	(start code)
	var drag = new Drag.Multi({
		handle: $('handle'),
		
		onBeforeStart: function(){
			var size = $(document.body).getSize().scrollSize;
			this.shade = new Element('div', {
				styles: {
					position: 'absolute',
					top: 0,
					left: 0,
					width: size.x,
					height: size.y,
					cursor: 'move',
					'z-index': 100
				}
			}).inject(document.body);
		},

		onStart: function(){
			$each(arguments, function(el){
				el.addClass('ondrag');
			});
		},

		onComplete: function(){
			this.shade.remove();
			$each(arguments, function(el){
				el.removeClass('ondrag');
			});
		}

	});

	drag.add($('object'), {
		'x': {
			limit: [0,440],
			style: 'margin-left'
		},
		'y': {
			limit: [0, 198],
			style: 'margin-top'
		}
	});
	(end)
*/

Drag.Multi = Drag.Base.extend({

	options: {
		handle: false,
		onStart: Class.empty,
		onBeforeStart: Class.empty,
		onComplete: Class.empty,
		onDrag: Class.empty,
		snap: 6
	},

	elementOptions: {
		unit: 'px',
		direction: 1,
		limit: false,
		grid: false,
		bind: false,
		fn: Drag.Transition.linear
	},

	initialize: function(options){
		this.setOptions(options);
		this.handle = $(this.options.handle);
		this.element = [];
		this.mouse = {'start': {}, 'now': {}};
		this.modifiers = {};
		this.bound = {
			'start': this.start.bindWithEvent(this),
			'check': this.check.bindWithEvent(this),
			'drag': this.drag.bindWithEvent(this),
			'stop': this.stop.bind(this)
		};
		this.attach();
		if (this.options.initialize) this.options.initialize.call(this);
	},

	/*
	Property: add
		Add element to modify its css properties based on the position of the mouse.

	Returns:
		Bind object.

	Arguments:
		el - the $(element) to apply the transformations to.
		options - The options object.
		bind - The Bind object (see <Bind> below).

	Options:
		x - optional, the Modifier object (see below).
		y - optional, the Modifier object (see below).

	Modifier:
		style - required, the style you want to modify when the mouse moves in an horizontal direction.
		direction - optional, 1 corresponds to positive direction (style change according to move movement), -1 inverse direction. defaults to 1.
		limit - optional, array with start and end limit for style value.
		grid - optional, distance in px for snap-to-grid dragging.
		fn - optional, object with two properties - direct and inverse functions
				(start code)
				{
					step: function(start, current, direction){ return direction * current - start; },
					inverse: function(start, current, direction){ return (start + current) / direction; }
				}
				(end code)

	Bind:
		x - optional, Bind object; change $(element) modifier value according to changes in Bind object.
		y - optional, Bind object; change $(element) modifier value according to changes in Bind object.
	*/

	add: function(el, options, bind){
		el = $(el);
		if (!$defined(bind)) bind = {};
		var result = {};
		for (var z in options){
			if ($type(options[z]) != 'object' || !$defined(options[z].style)) continue;
			if (!$defined(this.modifiers[z])) this.modifiers[z] = [];
			var mod = $merge(this.elementOptions, options[z], {modifier: z, element: el, bind: false, binded: false});
			if (bind[z]){ mod.bind = bind[z]; mod.bind.binded = true; }
			var sign = mod.style.slice(0, 1);
			if (sign == '-' || sign == '+'){
				mod.direction = (sign + 1).toInt();
				mod.style = mod.style.slice(1);
			}
			this.modifiers[z].push(mod);
			result[z] = mod;
		}
		if (!this.element.contains(el)) this.element.push(el);
		return result;
	},

	/*
	Property: remove
		Stop all transformations for the passed element.

	Arguments:
		el - the $(element) to stop transformations for.
	*/

	remove: function(el){
		el = $(el);
		for (var z in this.modifiers) this.modifiers[z] = this.modifiers[z].filter(function(e){ return el != e.element; });
		this.element.remove(el);
		return this;
	},

	/*
	Property: detach
		Stop transformations described by the argument.

	Arguments:
		mod - Bind object returned by <Drag.Multi::add>
	*/

	detach: function(mod){
		for (var z in mod) if ($type(mod[z]) == 'object' && !mod[z].binded) this.modifiers[z].remove(mod[z]);
		return this;
	},

	start: function(event){
		this.fireEvent('onBeforeStart', this.element);
		this.mouse.start = event.page;
		for (var z in this.modifiers){
			var mouse = this.mouse.start[z];
			this.modifiers[z].each(function(mod){
				mod.now = mod.element.getStyle(mod.style).toInt();
				mod.start = mod.fn.step(mod.now, mouse, mod.direction, true);
				mod.$limit = [];
				var limit = mod.limit;
				if (limit) for (var i = 0; i < 2; i++){
					if ($chk(limit[i])) mod.$limit[i] = ($type(limit[i]) == 'function') ? limit[i](mod) : limit[i];
				}
			}, this);
		}
		document.addListener('mousemove', this.bound.check);
		document.addListener('mouseup', this.bound.stop);
		this.fireEvent('onStart', this.element);
		event.stop();
	},

	modifierUpdate: function(mod){
		var z = mod.modifier, mouse = this.mouse.now[z];
		mod.out = false;
		mod.now = mod.fn.step(mod.start, mod.bind ? mod.bind.inverse : mouse, mod.direction);
		if (mod.$limit && $chk(mod.$limit[1]) && (mod.now > mod.$limit[1])){
			mod.now = mod.$limit[1];
			mod.out = true;
		} else if (mod.$limit && $chk(mod.$limit[0]) && (mod.now < mod.$limit[0])){
			mod.now = mod.$limit[0];
			mod.out = true;
		}
		if (mod.grid) mod.now -= ((mod.now + mod.grid/2) % mod.grid) - mod.grid/2;
		if (mod.binded) mod.inverse = mod.fn.inverse(mod.start, mod.now, mod.direction);
		mod.element.setStyle(mod.style, mod.now + mod.unit);
	},

	drag: function(event){
		this.mouse.now = event.page;
		for (var z in this.modifiers) this.modifiers[z].each(this.modifierUpdate, this);
		this.fireEvent('onDrag', this.element);
		event.stop();
	}

});
/*
Script: Drag.Resize.js
	Mootools Drag extension class for creating elements resizable in 8 directions.
	Contains <Drag.Resize>, <Element::makeResizable>.
*/

Drag.Multi.$direction = {
	east: { 'x':1 },
	west: { 'x':-1 },
	north: { 'y':-1 },
	south: { 'y':1 },
	nw: { 'x':-1, 'y':-1 },
	ne: { 'x':1, 'y':-1 },
	sw: { 'x':-1, 'y':1 },
	se: { 'x':1, 'y':1 }
};

/*
Class: Drag.Resize
	Extends <Drag.Base>, has additional functionality for resizing an element into 8 direction.

Arguments:
	el - the $(element) to apply the resize to.
	options - the options object.

Options:
	zIndex - optional, resize shade z-index;
	moveLimit - object, limit for element moving (resize in negative directions), see Limit below;
	resizeLimit - object, limit for element resizing, see Limit below;
	grid - optional, distance in px for snap-to-grid dragging;
	modifiers - an object. see Modifiers below;
	container - an element, will fill automatically limiting options based on the $(element) size and position. if false no limiting is applied. defaults to null (parentNode);
	preserveRatio - boolean, preserve initial element aspect ratio during resize. defaults to false;
	ghost - optional, show wired ghpot element during resize and update the element size and position after resize is completed;
	snap - optional, the distance you have to drag before the element starts to respond to the drag. defaults to 6;
	direction - object, see Direction below;
	limiter - object, see Limiter below;
	moveLimiter - object, see Limiter below;
	ghostClass - optional, wired ghost element class name;
	classPrefix - optional, class name prefix to add to sizer elements;
	hoverClass - optional, class name added to element onmouserover;
	shadeBackground - optional, background CSS property value for resize shade element (contains path to 1x1 px transparent gif image file);

Direction:
	east - east direction: { 'x':1 },
	west - west direction: { 'x':-1 },
	north - north direction: { 'y':-1 },
	south - south direction: { 'y':1 },
	nw - north-west direction: { 'x':-1, 'y':-1 },
	ne - north-east direction: { 'x':1, 'y':-1 },
	sw - south-west direction: { 'x':-1, 'y':1 },
	se - south-east direction: { 'x':1, 'y':1 }

Limiter:
	x - internal; {'-1': ['left', 'right'], '1': ['right', 'left']},
	y - internal; {'-1': ['top', 'bottom'], '1': ['bottom', 'top']}

Events:
	onBuild - optional, function to execute when resize handle is built;
	onBeforeStart - optional, function to execute when the user starts resizing but before initial properties values are calculated;
	onStart - optional, function to execute when the user starts resizing;
	onResize - optional, function to execute at every resize step;
	onComplete - optional, function to execute when the user completes the resize;
*/

Drag.Resize = new Class({

	options:{
		zIndex: 10000,
		moveLimit: false,
		resizeLimit: {'x': [0], 'y': [0]},
		grid: false,
		modifiers: {'x': 'left', 'y': 'top', 'width': 'width', 'height': 'height'},
		container: null, // false == no caintainer, null == container is parentNode
		preserveRatio: false,
		ghost: false,
		snap: 6,
		direction: Drag.Multi.$direction,
		limiter:{
			'x': {'-1': ['left', 'right'], '1': ['right', 'left']},
			'y': {'-1': ['top', 'bottom'], '1': ['bottom', 'top']}
		},
		moveLimiter:{
			'x': ['left', 'right'],
			'y': ['top', 'bottom']
		},
		ghostClass: 'ghost-sizer sizer-visible',
		classPrefix: 'sizer sizer-',
		hoverClass: 'sizer-visible',
		shadeBackground: 'transparent url(s.gif)',

		onBuild: Class.empty,
		onBeforeStart: Class.empty,
		onStart: Class.empty,
		onSnap: Class.empty,
		onResize: Class.empty,
		onComplete: Class.empty
	},

	initialize: function(el, options){
		var self = this;
		this.element = this.el = $(el);
		this.fx = {};
		this.binds = {};
		this.bound = {};
		this.setOptions(options);
		this.options.container = this.options.container === null ? this.el.getParent() : $(this.options.container);
		if ($type(this.options.direction) == 'string'){
			if (dir == 'all'){
				this.options.direction = Drag.Multi.$direction;
			} else {
				var dir = this.options.direction.split(/\s+/);
				this.options.direction = {};
				dir.each(function(d){ this[d] = Drag.Multi.$direction[d]; }, this.options.direction);
			}
		}
		var ce = this.el.getCoordinates(), positionStyle = this.el.getStyle('position');
		this.el.setStyles({'width': ce.width, 'height': ce.height});
		if (this.options.container){
			if (!(['relative', 'fixed'].contains(positionStyle))){
				var cc = this.options.container.getCoordinates();
				this.el.setStyles({'left': ce.left - cc.left, 'top': ce.top - cc.top});
			}
			this.options.moveLimit = $merge({'x': [0], 'y': [0]}, this.options.moveLimit);
		}
		if (this.options.preserveRatio){
			var R = ce.width / ce.height;
			// fix limits according to aspect ratio
			// FIXME how to process dynamic limits?
			// border limits do not work well too...
			var rlim = self.options.resizeLimit;
			var fix = function(z1, z2, op, no, coeff){
				if(rlim && rlim[z1] && rlim[z2] && rlim[z1][no] && rlim[z2][no])
					rlim[z1][no] = Math[op]( rlim[z1][no], coeff * rlim[z2][no] );
			};
			fix('x','y','max',0,R);
			fix('y','x','max',0,1/R);
			fix('x','y','min',1,R);
			fix('y','x','min',1,1/R);
			this.aspectStep = {
				x: {step: function(s, c, d){ return d * c / R - s; }},
				y: {step: function(s, c, d){ return d * c * R - s; }}
			};
			this.options.direction = $merge(this.options.direction);
			['nw','ne','sw','se'].each(function(z){ delete this[z]; }, this.options.direction);
		}
		if (this.options.ghost){
			this.ghost = new Element('div', {'class': this.options.ghostClass, 'styles': {'display': 'none'}}).injectAfter(this.el);
			for (var d in this.options.direction) this.ghost.adopt(new Element('div', {'class': this.options.classPrefix + d}));
		}
		var rOpts = {
			snap: this.options.snap,
			onBeforeStart: function(){
				self.fireEvent('onBeforeStart', this);
				self.started = true;
				this.shade = new Fx.Overlay(window, {'styles': {
					'position': positionStyle,
					'cursor': this.options.handle.getStyle('cursor'),
					'background': self.options.shadeBackground,
					'z-index': self.options.zIndex + 1
				}}).show();
				if (self.ghost){
					var ce = self.el.getCoordinates();
					self.ghost.setStyles({
						'display': 'block',
						'z-index': self.options.zIndex,
						'left': self.el.getStyle('left'),
						'top': self.el.getStyle('top'),
						'width': ce.width,
						'height': ce.height
					});
					for (var z in this.modifiers)
						this.modifiers[z].each(function(mod){
							if (mod.element === self.ghost)
								mod.element.setStyle(mod.style, self.el.getStyle(mod.style));
						});
					if (self.options.hoverClass) self.el.removeClass(self.options.hoverClass);
				}
			},
			onSnap: function(){
				self.fireEvent('onSnap', this);
			},
			onStart: function(){
				self.fireEvent('onStart', this);
			},
			onDrag: function(){
				self.fireEvent('onResize', this);
			},
			onComplete: function(){
				self.started = false;
				if (self.options.hoverClass) self.el.removeClass(self.options.hoverClass);
				this.shade.destroy();
				if (self.ghost){
					for (var z in this.modifiers){
						this.modifiers[z].each(function(mod){
							if (mod.element === self.ghost) self.el.setStyle(mod.style, mod.now + mod.unit);
						});
					}
					self.ghost.setStyle('display', 'none');
				}
				self.fireEvent('onComplete', this);
			}
		};
		var rlimitFcn = function(sign, props, limit){
			if (!self.options.container) return limit;
			if (!limit) limit = [0];
			var generator = function(lim){
				return function(mod){
					var cc = self.options.container.getCoordinates(),
						ec = mod.element.getCoordinates();
					var value = sign * (cc[props[0]] - ec[props[1]]);
					switch ($type(lim)){
						case 'number': return Math.min(value, lim);
						case 'function': return Math.min(value, lim(mod));
						default: return value;
					}
				};
			};
			return [limit[0], generator(limit[1])];
		};
		var mlimitFcn = function(props, limit, rlimit){
			var container = self.options.container;
			var generator = function(lim, rlim, op, rdef){
				if (!$type(rlim)) rlim = rdef;
				var lim_type = $type(lim);
				if (rlim === null) return lim_type == 'function' ? lim : function(){ return lim; };
				return function(mod){
					var cc = container.getCoordinates(),
						ec = mod.element.getCoordinates();
					var value = ec[props[1]] - cc[props[0]] - rlim;
					switch (lim_type){
						case 'number': return Math[op](value, lim);
						case 'function': return Math[op](value, lim(mod));
						default: return value;
					}
				};
			};
			if (!container){
				if (!limit) limit=false;
				container = self.el.getParent();
			} else if (!limit) limit=[0];
			return [generator(limit[0],rlimit[1],'max',null), generator(limit[1],rlimit[0],'min',limit[1])];
		};
		var opt = this.options, el = this.ghost ? this.ghost : this.el;
		if ($type(opt.grid) == 'number') opt.grid = {'x': opt.grid, 'y': opt.grid};
		for (var d in opt.direction){
			var mod = opt.direction[d];
			rOpts.handle = new Element('div', {'class': opt.classPrefix + d});
			var drag = this.fx[d] = new Drag.Multi(rOpts);
			var resizeLimit = {
				'x': rlimitFcn(mod.x, opt.limiter.x['' + mod.x], opt.resizeLimit.x),
				'y': rlimitFcn(mod.y, opt.limiter.y['' + mod.y], opt.resizeLimit.y)
			};
			var moveOpts = {};
			for (var z in mod){
				if (mod[z] < 0){
					moveOpts[z] = {
						limit: mlimitFcn(opt.moveLimiter[z], opt.moveLimit[z], opt.resizeLimit[z]),
						style: opt.modifiers[z],
						grid: opt.grid.x
					};
				}
			}
			var binds = {move: drag.add(el, moveOpts)}, resize = {opts: {}, bind: {}};
			this.binds[d] = binds;
			if ($defined(mod.x)){
				resize.opts.x = {
					limit: mod.x < 0 ? false : resizeLimit.x,
					grid: mod.x < 0 ? false : opt.grid.x,
					style: opt.modifiers.width,
					direction: mod.x
				};
				if (mod.x < 0) resize.bind.x = binds.move.x;
			}
			if ($defined(mod.y)){
				resize.opts.y = {
					limit: mod.y < 0 ? false : resizeLimit.y,
					grid: mod.y < 0 ? false : opt.grid.y,
					style: opt.modifiers.height,
					direction: mod.y
				};
				if (mod.y < 0) resize.bind.y = binds.move.y;
			}
			binds.resize = drag.add(el, resize.opts, resize.bind);
			if (opt.preserveRatio){
				var aspect = {
					'x': {
						fn: this.aspectStep.x,
						style: ($defined(mod.x)) ? opt.modifiers.height : null,
						direction: mod.x
					},
					'y': {
						fn: this.aspectStep.y,
						style: ($defined(mod.y)) ? opt.modifiers.width : null,
						direction: mod.y
					}
				};
				binds.aspect = drag.add(el, aspect, binds.resize);
			}
			this.fireEvent('onBuild', [d, binds]);
		}
		this.bound = (!this.options.hoverClass) ? {} : {
			'mouseenter': function(ev){
				this.addClass(self.options.hoverClass);
			},
			'mouseleave': function(ev){
				if(!self.started) this.removeClass(self.options.hoverClass);
			}
		};
		this.attach();
		if (this.options.initialize) this.options.initialize();
	},

	/*
	Property: add
		Call given function for each <Drag.Multi> instance created by <Drag.Resize>. Emulates onBuild event execution.

	Arguments:
		callback - the callback function called with arguments [direction, bind]
	*/

	add: function(callback){
		for (var d in this.options.direction)
			callback.call(this, d, this.binds[d]);
	},

	/*
	Property: attach
		Attach the effect to the element.
	*/

	attach: function(){
		$each(this.bound, function(fn, ev){ this.addEvent(ev, fn) }, this.el);
		for (var z in this.fx) this.element.adopt(this.fx[z].handle);
		return this;
	},

	/*
	Property: detach
		Detach the effect from the element.
	*/

	detach: function(){
		$each(this.bound, function(fn, ev){ this.removeEvent(ev, fn) }, this.el);
		for (var z in this.fx) this.fx[z].handle.remove();
		return this;
	},

	/*
	Property: stop
		Stop the effect and collect the garbage.
	*/

	stop: function(){
		this.detach();
		var garbage = [this.ghost];
		for (var z in this.fx) garbage.push(this.fx[z].handle);
		Garbage.trash(garbage);
		this.fx = this.bound = this.binds = {};
	}

});
Drag.Resize.implement(new Events, new Options);

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/

Element.extend({

	/*
	Property: makeResizable
		Makes an element resizable (by dragging) with the supplied options.

	Arguments:
		options - see <Drag.Resize> and <Drag.Base> for acceptable options. Falls back to <Drag.Base> if handle options set.
	*/

	makeResizable: function(options){
		options = options || {};
		if (options.handle)
			return new Drag.Base(this, $merge({modifiers: {'x': 'width', 'y': 'height'}}, options));
		return new Drag.Resize(this, options);
	}

});
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
