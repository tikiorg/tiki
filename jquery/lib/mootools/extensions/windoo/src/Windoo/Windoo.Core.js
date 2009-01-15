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
